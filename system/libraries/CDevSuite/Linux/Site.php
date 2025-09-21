<?php

class CDevSuite_Linux_Site extends CDevSuite_Site {
    /**
     * Get all certificates from config folder.
     *
     * @param string $path
     *
     * @return \CCollection
     */
    public function getCertificates($path = null) {
        $path = $path ?: $this->certificatesPath();

        return c::collect($this->files->scanDir($path))->filter(function ($value, $key) {
            return cstr::endsWith($value, '.crt');
        })->map(function ($cert) {
            return substr($cert, 0, -9);
        })->flip();
    }

    /**
     * Return http port suffix.
     *
     * @return string
     */
    public function httpSuffix() {
        $port = $this->config->get('port', 80);

        return ($port == 80) ? '' : ':' . $port;
    }

    /**
     * Return https port suffix.
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
     *
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
     * @param string     $url
     * @param null|mixed $siteConf
     * @param mixed      $certificateExpireInDays
     * @param mixed      $caExpireInYears
     *
     * @return void
     */
    public function secure($url, $siteConf = null, $certificateExpireInDays = 396, $caExpireInYears = 20) {
        $this->unsecure($url);

        $this->files->ensureDirExists($this->caPath(), CDevSuite::user());

        $this->files->ensureDirExists($this->certificatesPath(), CDevSuite::user());

        $this->files->ensureDirExists($this->nginxPath(), CDevSuite::user());
        $caExpireInDate = (new \DateTime())->diff(new \DateTime("+{$caExpireInYears} years"));
        $this->createCa($caExpireInDate->format('%a'));

        $this->createCertificate($url, $certificateExpireInDays);

        $this->createSecureNginxServer($url);
        $this->files->putAsUser(
            $this->nginxPath($url),
            $this->buildSecureNginxServer($url, $siteConf)
        );
    }

    /**
     * If CA and root certificates are nonexistent, create them and trust the root cert.
     *
     * @param mixed $caExpireInDays
     *
     * @return void
     */
    public function createCa($caExpireInDays) {
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
            'openssl req -new -newkey rsa:2048 -days %s -nodes -x509 -subj "/O=%s/commonName=%s/organizationalUnitName=Developers/emailAddress=%s/" -keyout "%s" -out "%s"',
            $caExpireInDays,
            $oName,
            $cName,
            'rootcertificate@cf.devsuite',
            $caKeyPath,
            $caPemPath
        ));
        // $this->cli->runAsUser(sprintf(
        //     'openssl genrsa -des3 -out %s 2048',
        //     $caKeyPath
        // ));
        // $this->cli->runAsUser(sprintf(
        //     'openssl req -x509 -new -nodes -key %s -sha256 -days 825 -out %s',
        //     $caKeyPath,
        //     $caPemPath
        // ));
    }

    /**
     * Create and trust a certificate for the given URL.
     *
     * @param string $url
     * @param mixed  $caExpireInDays
     *
     * @return void
     */
    public function createCertificate($url, $caExpireInDays) {
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
            'openssl x509 -req -sha256 -days %s -CA "%s" -CAkey "%s" %s -in "%s" -out "%s" -extensions v3_req -extfile "%s"',
            $caExpireInDays,
            $caPemPath,
            $caKeyPath,
            $caSrlParam,
            $csrPath,
            $crtPath,
            $confPath
        );

        $this->cli->runAsUser($commandOpenSSL);

        $this->trustCertificate($crtPath, $url);
    }

    /**
     * Create the private key for the TLS certificate.
     *
     * @param string $keyPath
     *
     * @return void
     */
    public function createPrivateKey($keyPath) {
        $this->cli->runAsUser(sprintf('openssl genrsa -out %s 2048', $keyPath));
    }

    /**
     * Create the signing request for the TLS certificate.
     *
     * @param string $keyPath
     * @param mixed  $url
     * @param mixed  $csrPath
     * @param mixed  $confPath
     *
     * @return void
     */
    public function createSigningRequest($url, $keyPath, $csrPath, $confPath) {
        $this->cli->runAsUser(sprintf(
            'openssl req -new -key %s -out %s -subj "/C=US/ST=MN/O=DevSuite/localityName=DevSuite/commonName=%s/organizationalUnitName=DevSuite/emailAddress=devsuite/" -config %s -passin pass:',
            $keyPath,
            $csrPath,
            $url,
            $confPath
        ));
    }

    /**
     * Build the SSL config for the given URL.
     *
     * @param string $url
     * @param mixed  $path
     *
     * @return string
     */
    public function buildCertificateConf($path, $url) {
        $config = str_replace('DEVSUITE_DOMAIN', $url, $this->files->get(CDevSuite::stubsPath() . 'openssl.conf'));
        $this->files->putAsUser($path, $config);
    }

    /**
     * @param $url
     */
    public function createSecureNginxServer($url) {
        $this->files->putAsUser(
            CDevSuite::homePath() . '/Nginx/' . $url,
            $this->buildSecureNginxServer($url)
        );
    }

    /**
     * Build the TLS secured Nginx server for the given URL.
     *
     * @param string $url
     *
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
            ],
            [
                rtrim(CDevSuite::homePath(), '/'),
                CDevSuite::serverPath(),
                CDevSuite::staticPrefix(),
                $url,
                $path . '/' . $url . '.crt',
                $path . '/' . $url . '.key',
                $this->config->get('port', 80),
                $this->config->get('https_port', 443),
                $this->httpsSuffix(),
            ],
            $this->files->get(CDevSuite::stubsPath() . 'secure.devsuite.conf')
        );

        return $content;
    }

    /**
     * Unsecure the given URL so that it will use HTTP again.
     *
     * @param string $url
     *
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
     * Regenerate all secured file configurations.
     *
     * @return void
     */
    public function regenerateSecuredSitesConfig() {
        c::collect($this->secured())->each(function ($url) {
            $this->createSecureNginxServer($url);
        });
    }

    /**
     * Trust the given certificate file in the Mac Keychain.
     *
     * @param string $crtPath
     * @param mixed  $url
     *
     * @return void
     */
    public function trustCertificate($crtPath, $url = null) {
        $this->cli->runAsUser(sprintf(
            'certutil -d sql:$HOME/.pki/nssdb -A -t TC -n "%s" -i "%s"',
            $url,
            $crtPath
        ));

        $this->cli->run(sprintf(
            'certutil -d $HOME/.mozilla/firefox/*.default -A -t TC -n "%s" -i "%s"',
            $url,
            $crtPath
        ));
    }
}
