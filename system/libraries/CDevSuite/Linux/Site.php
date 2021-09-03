<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite_Linux_Site extends CDevSuite_Site {

    /**
     * Get all certificates from config folder.
     *
     * @param string $path
     * @return \Illuminate\Support\Collection
     */
    public function getCertificates($path) {
        return c::collect($this->files->scanDir($path))->filter(function ($value, $key) {
                    return cstr::endsWith($value, '.crt');
                })->map(function ($cert) {
                    return substr($cert, 0, -9);
                })->flip();
    }

    /**
     * Return http port suffix
     *
     * @return string
     */
    public function httpSuffix() {
        $port = $this->config->get('port', 80);

        return ($port == 80) ? '' : ':' . $port;
    }

    /**
     * Return https port suffix
     *
     * @return string
     */
    public function httpsSuffix() {
        $port = $this->config->get('https_port', 443);

        return ($port == 443) ? '' : ':' . $port;
    }

    /**
     * Resecure all currently secured sites with a fresh domain.
     *
     * @param string $oldDomain
     * @param string $domain
     * @return void
     */
    public function resecureForNewDomain($oldDomain, $domain) {
        if (!$this->files->exists($this->certificatesPath())) {
            return;
        }

        $secured = $this->secured();

        foreach ($secured as $url) {
            $this->unsecure($url);
        }

        foreach ($secured as $url) {
            $this->secure(str_replace('.' . $oldDomain, '.' . $domain, $url));
        }
    }

    /**
     * Secure the given host with TLS.
     *
     * @param string $url
     * @return void
     */
    public function secure($url) {
        $this->unsecure($url);

        $this->files->ensureDirExists($this->caPath(), CDevSuite::user());

        $this->files->ensureDirExists($this->certificatesPath(), CDevSuite::user());

        $this->createCa();

        $this->createCertificate($url);

        $this->createSecureNginxServer($url);
    }

    /**
     * If CA and root certificates are nonexistent, create them and trust the root cert.
     *
     * @return void
     */
    public function createCa() {
        $caPemPath = $this->caPath('CFDevSuiteCASelfSigned.pem');
        $caKeyPath = $this->caPath('CFDevSuiteCASelfSigned.key');

        if ($this->files->exists($caKeyPath) && $this->files->exists($caPemPath)) {
            return;
        }

        $oName = 'CF DevSuite CA Self Signed Organization';
        $cName = 'CF DevSuite CA Self Signed CN';

        if ($this->files->exists($caKeyPath)) {
            $this->files->unlink($caKeyPath);
        }
        if ($this->files->exists($caPemPath)) {
            $this->files->unlink($caPemPath);
        }
        $this->cli->runAsUser(sprintf(
                        'openssl req -new -newkey rsa:2048 -days 730 -nodes -x509 -subj "/O=%s/commonName=%s/organizationalUnitName=Developers/emailAddress=%s/" -keyout "%s" -out "%s"', $oName, $cName, 'rootcertificate@cf.devsuite', $caKeyPath, $caPemPath
        ));
    }

    /**
     * Create and trust a certificate for the given URL.
     *
     * @param string $url
     * @return void
     */
    public function createCertificate($url) {
        $caPemPath = $this->caPath() . '/CFDevSuiteCASelfSigned.pem';
        $caKeyPath = $this->caPath() . '/CFDevSuiteCASelfSigned.key';
        $caSrlPath = $this->caPath() . '/CFDevSuiteCASelfSigned.srl';
        $keyPath = $this->certificatesPath() . '/' . $url . '.key';
        $csrPath = $this->certificatesPath() . '/' . $url . '.csr';
        $crtPath = $this->certificatesPath() . '/' . $url . '.crt';
        $confPath = $this->certificatesPath() . '/' . $url . '.conf';

        $this->buildCertificateConf($confPath, $url);
        $this->createPrivateKey($keyPath);
        $this->createSigningRequest($url, $keyPath, $csrPath, $confPath);

        $caSrlParam = ' -CAcreateserial';
        if ($this->files->exists($caSrlPath)) {
            $caSrlParam = ' -CAserial ' . $caSrlPath;
        }
        
        $commandOpenSSL = sprintf(
                        'openssl x509 -req -sha256 -days 365 -CA "%s" -CAkey "%s"%s -in "%s" -out "%s" -extensions v3_req -extfile "%s"', $caPemPath, $caKeyPath, $caSrlParam, $csrPath, $crtPath, $confPath
        );
        $this->cli->runAsUser($commandOpenSSL);

        $this->trustCertificate($crtPath, $url);
    }

    /**
     * Create the private key for the TLS certificate.
     *
     * @param string $keyPath
     * @return void
     */
    public function createPrivateKey($keyPath) {
        $this->cli->runAsUser(sprintf('openssl genrsa -out %s 2048', $keyPath));
    }

    /**
     * Create the signing request for the TLS certificate.
     *
     * @param string $keyPath
     * @return void
     */
    public function createSigningRequest($url, $keyPath, $csrPath, $confPath) {
        $this->cli->runAsUser(sprintf(
                        'openssl req -new -key %s -out %s -subj "/C=US/ST=MN/O=DevSuite/localityName=DevSuite/commonName=%s/organizationalUnitName=DevSuite/emailAddress=devsuite/" -config %s -passin pass:', $keyPath, $csrPath, $url, $confPath
        ));
    }

    /*     * tap
     * Build the SSL config for the given URL.
     *
     * @param string $url
     * @return string
     */

    public function buildCertificateConf($path, $url) {
        $config = str_replace('DEVSUITE_DOMAIN', $url, $this->files->get(CDevSuite::stubsPath() . 'openssl.conf'));
        $this->files->putAsUser($path, $config);
    }

    /**
     * Trust the given certificate file in the Mac Keychain.
     *
     * @param string $crtPath
     * @return void
     */
    public function trustCertificate($crtPath, $url) {
        $commandTrust1 = sprintf(
                        'certutil -d sql:$HOME/.pki/nssdb -A -t TC -n "%s" -i "%s"', $url, $crtPath
        ); 
        
        $commandTrust2 = sprintf(
                        'certutil -d $HOME/.mozilla/firefox/*.default -A -t TC -n "%s" -i "%s"', $url, $crtPath
        ); 
        
        CDevSuite::info('Executing Command:'.$commandTrust1);
        
        $this->cli->run($commandTrust1);

        CDevSuite::info('Executing Command:'.$commandTrust2);
        
        $this->cli->run($commandTrust2);
    }

    /**
     * @param $url
     */
    public function createSecureNginxServer($url) {
        $this->files->putAsUser(
                CDevSuite::homePath() . '/Nginx/' . $url, $this->buildSecureNginxServer($url)
        );
    }

    /**
     * Build the TLS secured Nginx server for the given URL.
     *
     * @param string $url
     * @return string
     */
    public function buildSecureNginxServer($url) {
        $path = $this->certificatesPath();

        $content = str_replace(
                [
                    'DEVSUITE_HOME_PATH',
                    'DEVSUITE_SERVER_PATH',
                    'DEVSUITE_STATIC_PREFIX',
                    'DEVSUITE_SITE',
                    'DEVSUITE_CERT',
                    'DEVSUITE_KEY',
                    'DEVSUITE_HTTP_PORT',
                    'DEVSUITE_HTTPS_PORT',
                    'DEVSUITE_REDIRECT_PORT',
                ]
                , [
            rtrim(CDevSuite::homePath(), '/'),
            CDevSuite::serverPath(),
            CDevSuite::staticPrefix(),
            $url,
            $path . '/' . $url . '.crt',
            $path . '/' . $url . '.key',
            $this->config->get('port', 80),
            $this->config->get('https_port', 443),
            $this->httpsSuffix(),
                ]
                , $this->files->get(CDevSuite::stubsPath() . 'secure.devsuite.conf')
        );

        return $content;
    }

    /**
     * Unsecure the given URL so that it will use HTTP again.
     *
     * @param string $url
     * @return void
     */
    public function unsecure($url) {
        if ($this->files->exists($this->certificatesPath() . '/' . $url . '.crt')) {
            $this->files->unlink(CDevSuite::homePath() . '/Nginx/' . $url);

            $this->files->unlink($this->certificatesPath() . '/' . $url . '.conf');
            $this->files->unlink($this->certificatesPath() . '/' . $url . '.key');
            $this->files->unlink($this->certificatesPath() . '/' . $url . '.csr');
            $this->files->unlink($this->certificatesPath() . '/' . $url . '.crt');

            $this->cli->run(sprintf('certutil -d sql:$HOME/.pki/nssdb -D -n "%s"', $url));
            $this->cli->run(sprintf('certutil -d $HOME/.mozilla/firefox/*.default -D -n "%s"', $url));
        }
    }

    /**
     * Regenerate all secured file configurations
     *
     * @return void
     */
    public function regenerateSecuredSitesConfig() {
        $this->secured()->each(function ($url) {
            $this->createSecureNginxServer($url);
        });
    }

}
