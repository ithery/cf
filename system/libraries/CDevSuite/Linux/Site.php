<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite_Linux_Site extends CDevSuite_Site {
    public $config;
    public $cli;
    public $files;

    /**
     * Create a new Site instance.
     *
     * @param Configuration $config
     * @param CommandLine   $cli
     * @param Filesystem    $files
     */
    public function __construct()
    {
        $this->cli = CDevSuite::commandLine();
        $this->files = CDevSuite::filesystem();
        $this->config = CDevSuite::configuration();
    }

    /**
     * Get the real hostname for the given path, checking links.
     *
     * @param string $path
     * @return string|null
     */
    public function host($path)
    {
        foreach ($this->files->scandir($this->sitesPath()) as $link) {
            if (realpath($this->sitesPath() . '/' . $link) === $path) {
                return $link;
            }
        }

        return basename($path);
    }

    /**
     * Link the current working directory with the given name.
     *
     * @param string $target
     * @param string $link
     * @return string
     */
    public function link($target, $link)
    {
        $this->files->ensureDirExists(
            $linkPath = $this->sitesPath(),
            CDevSuite::user()
        );

        $this->config->prependPath($linkPath);

        $this->files->symlinkAsUser($target, $linkPath . '/' . $link);

        return $linkPath . '/' . $link;
    }

    /**
     * Pretty print out all links in DevSuite.
     *
     * @return CCollection
     */
    public function links()
    {
        $certsPath = CDevSuite::homePath() . '/Certificates';

        $this->files->ensureDirExists($certsPath, CDevSuite::user());

        $certs = $this->getCertificates($certsPath);

        return $this->getLinks(CDevSuite::homePath() . '/Sites', $certs);
    }

    /**
     * Get all certificates from config folder.
     *
     * @param string $path
     * @return \Illuminate\Support\Collection
     */
    public function getCertificates($path)
    {
        return c::collect($this->files->scanDir($path))->filter(function ($value, $key) {
            return ends_with($value, '.crt');
        })->map(function ($cert) {
            return substr($cert, 0, -9);
        })->flip();
    }

    /**
     * Get list of links and present them formatted.
     *
     * @param string                         $path
     * @param \Illuminate\Support\Collection $certs
     * @return \Illuminate\Support\Collection
     */
    public function getLinks($path, $certs)
    {
        $config = $this->config->read();

        $httpPort = $this->httpSuffix();
        $httpsPort = $this->httpsSuffix();

        return collect($this->files->scanDir($path))->mapWithKeys(function ($site) use ($path) {
            return [$site => $this->files->readLink($path . '/' . $site)];
        })->map(function ($path, $site) use ($certs, $config, $httpPort, $httpsPort) {
            $secured = $certs->has($site);

            $url = ($secured ? 'https' : 'http') . '://' . $site . '.' . $config['domain'] . ($secured ? $httpsPort : $httpPort);

            return [$site, $secured ? ' X' : '', $url, $path];
        });
    }

    /**
     * Return http port suffix
     *
     * @return string
     */
    public function httpSuffix()
    {
        $port = $this->config->get('port', 80);

        return ($port == 80) ? '' : ':' . $port;
    }

    /**
     * Return https port suffix
     *
     * @return string
     */
    public function httpsSuffix()
    {
        $port = $this->config->get('https_port', 443);

        return ($port == 443) ? '' : ':' . $port;
    }

    /**
     * Unlink the given symbolic link.
     *
     * @param string $name
     * @return void
     */
    public function unlink($name)
    {
        if ($this->files->exists($path = $this->sitesPath() . '/' . $name)) {
            $this->files->unlink($path);
        }
    }

    /**
     * Remove all broken symbolic links.
     *
     * @return void
     */
    public function pruneLinks()
    {
        $this->files->ensureDirExists($this->sitesPath(), CDevSuite::user());

        $this->files->removeBrokenLinksAt($this->sitesPath());
    }

    /**
     * Resecure all currently secured sites with a fresh domain.
     *
     * @param string $oldDomain
     * @param string $domain
     * @return void
     */
    public function resecureForNewDomain($oldDomain, $domain)
    {
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
     * Get all of the URLs that are currently secured.
     *
     * @return \Illuminate\Support\Collection
     */
    public function secured()
    {
        return collect($this->files->scandir($this->certificatesPath()))
            ->map(function ($file) {
                return str_replace(['.key', '.csr', '.crt', '.conf'], '', $file);
            })->unique()->values();
    }

    /**
     * Secure the given host with TLS.
     *
     * @param string $url
     * @return void
     */
    public function secure($url)
    {
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
    public function createCa()
    {
        $caPemPath = $this->caPath() . '/CFDevSuiteCASelfSigned.pem';
        $caKeyPath = $this->caPath() . '/CFDevSuiteCASelfSigned.key';
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
            'openssl req -new -newkey rsa:2048 -days 730 -nodes -x509 -subj "/O=%s/commonName=%s/organizationalUnitName=Developers/emailAddress=%s/" -keyout "%s" -out "%s"',
            $oName,
            $cName,
            'rootcertificate@cf.devsuite',
            $caKeyPath,
            $caPemPath
        ));
    }

    /**
     * Create and trust a certificate for the given URL.
     *
     * @param string $url
     * @return void
     */
    public function createCertificate($url)
    {
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
        $this->cli->runAsUser(sprintf(
            'openssl x509 -req -sha256 -days 365 -CA "%s" -CAkey "%s"%s -in "%s" -out "%s" -extensions v3_req -extfile "%s"',
            $caPemPath,
            $caKeyPath,
            $caSrlParam,
            $csrPath,
            $crtPath,
            $confPath
        ));

        $this->trustCertificate($crtPath, $url);
    }

    /**
     * Create the private key for the TLS certificate.
     *
     * @param string $keyPath
     * @return void
     */
    public function createPrivateKey($keyPath)
    {
        $this->cli->runAsUser(sprintf('openssl genrsa -out %s 2048', $keyPath));
    }

    /**
     * Create the signing request for the TLS certificate.
     *
     * @param string $keyPath
     * @return void
     */
    public function createSigningRequest($url, $keyPath, $csrPath, $confPath)
    {
        $this->cli->runAsUser(sprintf(
            'openssl req -new -key %s -out %s -subj "/C=US/ST=MN/O=DevSuite/localityName=DevSuite/commonName=%s/organizationalUnitName=Valet/emailAddress=devsuite/" -config %s -passin pass:',
            $keyPath,
            $csrPath,
            $url,
            $confPath
        ));
    }

    /**tap
     * Build the SSL config for the given URL.
     *
     * @param string $url
     * @return string
     */
    public function buildCertificateConf($path, $url)
    {
        $config = str_replace('DEVSUITE_DOMAIN', $url, $this->files->get(CDevSuite::stubsPath(). 'linux/openssl.conf'));
        $this->files->putAsUser($path, $config);
    }

    /**
     * Trust the given certificate file in the Mac Keychain.
     *
     * @param string $crtPath
     * @return void
     */
    public function trustCertificate($crtPath, $url)
    {
        $this->cli->run(sprintf(
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

    /**
     * @param $url
     */
    public function createSecureNginxServer($url)
    {
        $this->files->putAsUser(
            CDevSuite::homePath() . '/Nginx/' . $url,
            $this->buildSecureNginxServer($url)
        );
    }

    /**
     * Build the TLS secured Nginx server for the given URL.
     *
     * @param string $url
     * @return string
     */
    public function buildSecureNginxServer($url)
    {
        $path = $this->certificatesPath();

        return str_array_replace(
            [
                'DEVSUITE_HOME_PATH' => CDevSuite::homePath(),
                'DEVSUITE_SERVER_PATH' => CDevSuite::serverPath(),
                'DEVSUITE_STATIC_PREFIX' => CDevSuite::staticPrefix(),
                'DEVSUITE_SITE' => $url,
                'DEVSUITE_CERT' => $path . '/' . $url . '.crt',
                'DEVSUITE_KEY' => $path . '/' . $url . '.key',
                'DEVSUITE_HTTP_PORT' => $this->config->get('port', 80),
                'DEVSUITE_HTTPS_PORT' => $this->config->get('https_port', 443),
                'DEVSUITE_REDIRECT_PORT' => $this->httpsSuffix(),
            ],
            $this->files->get(CDevSuite::stubsPath().'linux/secure.devsuite.conf')
        );
    }

    /**
     * Unsecure the given URL so that it will use HTTP again.
     *
     * @param string $url
     * @return void
     */
    public function unsecure($url)
    {
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
    public function regenerateSecuredSitesConfig()
    {
        $this->secured()->each(function ($url) {
            $this->createSecureNginxServer($url);
        });
    }

    /**
     * Get the path to the linked Dev Suite sites.
     *
     * @return string
     */
    public function sitesPath()
    {
        return CDevSuite::homePath() . '/Sites';
    }

    /**
     * Get the path to the Dev Suite CA certificates.
     *
     * @return string
     */
    public function caPath()
    {
        return CDevSuite::homePath() . '/CA';
    }

    /**
     * Get the path to the DevSuite TLS certificates.
     *
     * @return string
     */
    public function certificatesPath()
    {
        return CDevSuite::homePath() . '/Certificates';
    }
}