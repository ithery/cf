<?php

/**
 * Description of Site
 *
 * @author Hery
 */
class CDevSuite_Mac_Site extends CDevSuite_Site {
    /**
     * Get all certificates from config folder.
     *
     * @param string $path
     *
     * @return \CCollection
     */
    public function getCertificates($path = null) {
        $path = $path ?: $this->certificatesPath();

        $this->files->ensureDirExists($path, CDevSuite::user());

        $config = $this->config->read();

        return c::collect($this->files->scandir($path))->filter(function ($value, $key) {
            return cstr::endsWith($value, '.crt');
        })->map(function ($cert) use ($config) {
            $certWithoutSuffix = substr($cert, 0, -4);
            $trimToString = '.';

            // If we have the cert ending in our tld strip that tld specifically
            // if not then just strip the last segment for  backwards compatibility.
            if (cstr::endsWith($certWithoutSuffix, $config['tld'])) {
                $trimToString .= $config['tld'];
            }

            return substr($certWithoutSuffix, 0, strrpos($certWithoutSuffix, $trimToString));
        })->flip();
    }

    /**
     * Resecure all currently secured sites with a fresh tld.
     *
     * @param string $oldTld
     * @param string $tld
     *
     * @return void
     */
    public function resecureForNewTld($oldTld, $tld) {
        if (!$this->files->exists($this->certificatesPath())) {
            return;
        }

        $secured = $this->secured();

        foreach ($secured as $url) {
            $newUrl = str_replace('.' . $oldTld, '.' . $tld, $url);
            $siteConf = $this->getSiteConfigFileContents($url, '.' . $oldTld);

            if (!empty($siteConf) && strpos($siteConf, '# devsuite stub: proxy.devsuite.conf') === 0) {
                // proxy config
                $this->unsecure($url);
                $this->secure($newUrl, $this->replaceOldDomainWithNew($siteConf, $url, $newUrl));
            } else {
                // normal config
                $this->unsecure($url);
                $this->secure($newUrl);
            }
        }
    }

    /**
     * Resecure all currently secured sites with a fresh configuration.
     *
     * There are only two supported values: tld and loopback
     * And those must be submitted in pairs else unexpected results may occur.
     * eg: both $old and $new should contain the same indexes.
     *
     * @param array $old
     * @param array $new
     *
     * @return void
     */
    public function resecureForNewConfiguration(array $old, array $new) {
        if (!$this->files->exists($this->certificatesPath())) {
            return;
        }

        $secured = $this->secured();

        $defaultTld = $this->config->read()['tld'];
        $oldTld = !empty($old['tld']) ? $old['tld'] : $defaultTld;
        $tld = !empty($new['tld']) ? $new['tld'] : $defaultTld;

        $defaultLoopback = $this->config->read()['loopback'];
        $oldLoopback = !empty($old['loopback']) ? $old['loopback'] : $defaultLoopback;
        $loopback = !empty($new['loopback']) ? $new['loopback'] : $defaultLoopback;

        foreach ($secured as $url) {
            $newUrl = str_replace('.' . $oldTld, '.' . $tld, $url);
            $siteConf = $this->getSiteConfigFileContents($url, '.' . $oldTld);

            if (!empty($siteConf) && strpos($siteConf, '# valet stub: proxy.valet.conf') === 0) {
                // proxy config
                $this->unsecure($url);
                $this->secure(
                    $newUrl,
                    $this->replaceOldLoopbackWithNew(
                        $this->replaceOldDomainWithNew($siteConf, $url, $newUrl),
                        $oldLoopback,
                        $loopback
                    )
                );
            } else {
                // normal config
                $this->unsecure($url);
                $this->secure($newUrl);
            }
        }
    }

    /**
     * Parse Nginx site config file contents to swap old domain to new.
     *
     * @param string $siteConf Nginx site config content
     * @param string $old      Old domain
     * @param string $new      New domain
     *
     * @return string
     */
    public function replaceOldDomainWithNew($siteConf, $old, $new) {
        $lookups = [];
        $lookups[] = '~server_name .*;~';
        $lookups[] = '~error_log .*;~';
        $lookups[] = '~ssl_certificate_key .*;~';
        $lookups[] = '~ssl_certificate .*;~';

        foreach ($lookups as $lookup) {
            preg_match($lookup, $siteConf, $matches);
            foreach ($matches as $match) {
                $replaced = str_replace($old, $new, $match);
                $siteConf = str_replace($match, $replaced, $siteConf);
            }
        }
        return $siteConf;
    }

    /**
     * Parse Nginx site config file contents to swap old loopback address to new.
     *
     * @param string $siteConf Nginx site config content
     * @param string $old      Old loopback address
     * @param string $new      New loopback address
     *
     * @return string
     */
    public function replaceOldLoopbackWithNew($siteConf, $old, $new) {
        $shouldComment = $new === CDevSuite::loopback();

        $lookups = [];
        $lookups[] = '~#?listen .*:80; # valet loopback~';
        $lookups[] = '~#?listen .*:443 ssl http2; # valet loopback~';
        $lookups[] = '~#?listen .*:60; # valet loopback~';

        foreach ($lookups as $lookup) {
            preg_match($lookup, $siteConf, $matches);
            foreach ($matches as $match) {
                $replaced = str_replace($old, $new, $match);

                if ($shouldComment && strpos($replaced, '#') !== 0) {
                    $replaced = '#' . $replaced;
                }

                if (!$shouldComment) {
                    $replaced = ltrim($replaced, '#');
                }

                $siteConf = str_replace($match, $replaced, $siteConf);
            }
        }
        return $siteConf;
    }

    /**
     * Get all of the URLs that are currently secured.
     *
     * @return array
     */
    public function secured() {
        return c::collect($this->files->scandir($this->certificatesPath()))
            ->map(function ($file) {
                return str_replace(['.key', '.csr', '.crt', '.conf'], '', $file);
            })->unique()->values()->all();
    }

    /**
     * Secure the given host with TLS.
     *
     * @param string $url
     * @param string $siteConf pregenerated Nginx config file contents
     *
     * @return void
     */
    public function secure($url, $siteConf = null) {
        $this->unsecure($url);

        $this->files->ensureDirExists($this->caPath(), CDevSuite::user());

        $this->files->ensureDirExists($this->certificatesPath(), CDevSuite::user());

        $this->files->ensureDirExists($this->nginxPath(), CDevSuite::user());

        $this->createCa();

        $this->createCertificate($url);

        $this->files->putAsUser(
            $this->nginxPath($url),
            $this->buildSecureNginxServer($url, $siteConf)
        );
    }

    /**
     * If CA and root certificates are nonexistent, crete them and trust the root cert.
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

        $this->cli->run(sprintf(
            'sudo security delete-certificate -c "%s" /Library/Keychains/System.keychain',
            $cName
        ));

        $this->cli->runAsUser(sprintf(
            'openssl req -new -newkey rsa:2048 -days 730 -nodes -x509 -subj "/C=/ST=/O=%s/localityName=/commonName=%s/organizationalUnitName=Developers/emailAddress=%s/" -keyout "%s" -out "%s"',
            $oName,
            $cName,
            'rootcertificate@cf.devsuite',
            $caKeyPath,
            $caPemPath
        ));
        $this->trustCa($caPemPath);
    }

    /**
     * Create and trust a certificate for the given URL.
     *
     * @param string $url
     *
     * @return void
     */
    public function createCertificate($url) {
        $caPemPath = $this->caPath('CFDevSuiteCASelfSigned.pem');
        $caKeyPath = $this->caPath('CFDevSuiteCASelfSigned.key');
        $caSrlPath = $this->caPath('CFDevSuiteCASelfSigned.srl');
        $keyPath = $this->certificatesPath($url, 'key');
        $csrPath = $this->certificatesPath($url, 'csr');
        $crtPath = $this->certificatesPath($url, 'crt');
        $confPath = $this->certificatesPath($url, 'conf');

        $this->buildCertificateConf($confPath, $url);
        $this->createPrivateKey($keyPath);
        $this->createSigningRequest($url, $keyPath, $csrPath, $confPath);

        $caSrlParam = '-CAserial "' . $caSrlPath . '"';
        if (!$this->files->exists($caSrlPath)) {
            $caSrlParam .= ' -CAcreateserial';
        }

        $result = $this->cli->runAsUser(sprintf(
            'openssl x509 -req -sha256 -days 730 -CA "%s" -CAkey "%s" %s -in "%s" -out "%s" -extensions v3_req -extfile "%s"',
            $caPemPath,
            $caKeyPath,
            $caSrlParam,
            $csrPath,
            $crtPath,
            $confPath
        ));

        // If cert could not be created using runAsUser(), use run().
        if (strpos($result, 'Permission denied')) {
            $this->cli->run(sprintf(
                'openssl x509 -req -sha256 -days 730 -CA "%s" -CAkey "%s" %s -in "%s" -out "%s" -extensions v3_req -extfile "%s"',
                $caPemPath,
                $caKeyPath,
                $caSrlParam,
                $csrPath,
                $crtPath,
                $confPath
            ));
        }

        $this->trustCertificate($crtPath);
    }

    /**
     * Create the private key for the TLS certificate.
     *
     * @param string $keyPath
     *
     * @return void
     */
    public function createPrivateKey($keyPath) {
        $this->cli->runAsUser(sprintf('openssl genrsa -out "%s" 2048', $keyPath));
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
            'openssl req -new -key "%s" -out "%s" -subj "/C=/ST=/O=/localityName=/commonName=%s/organizationalUnitName=/emailAddress=%s%s/" -config "%s"',
            $keyPath,
            $csrPath,
            $url,
            $url,
            '@cf.devsuite',
            $confPath
        ));
    }

    /**
     * Trust the given root certificate file in the Mac Keychain.
     *
     * @param string $caPemPath
     *
     * @return void
     */
    public function trustCa($caPemPath) {
        $this->cli->run(sprintf(
            'sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain "%s"',
            $caPemPath
        ));
    }

    /**
     * Trust the given certificate file in the Mac Keychain.
     *
     * @param string $crtPath
     *
     * @return void
     */
    public function trustCertificate($crtPath) {
        $this->cli->run(sprintf(
            'sudo security add-trusted-cert -d -r trustAsRoot -k /Library/Keychains/System.keychain "%s"',
            $crtPath
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
     * Build the TLS secured Nginx server for the given URL.
     *
     * @param string $url
     * @param string $siteConf (optional) Nginx site config file content
     *
     * @return string
     */
    public function buildSecureNginxServer($url, $siteConf = null) {
        if ($siteConf === null) {
            $siteConf = $this->replaceOldLoopbackWithNew(
                $this->files->get(CDevSuite::stubsPath() . 'secure.devsuite.conf'),
                'DEVSUITE_LOOPBACK',
                $this->devsuiteLoopback()
            );
        }

        return str_replace(
            ['DEVSUITE_HOME_PATH', 'DEVSUITE_SERVER_PATH', 'DEVSUITE_STATIC_PREFIX', 'DEVSUITE_SITE', 'DEVSUITE_CERT', 'DEVSUITE_KEY'],
            [
                CDevSuite::homePath(),
                CDevSuite::serverPath(),
                CDevSuite::staticPrefix(),
                $url,
                $this->certificatesPath($url, 'crt'),
                $this->certificatesPath($url, 'key'),
            ],
            $siteConf
        );
    }

    /**
     * Unsecure the given URL so that it will use HTTP again.
     *
     * @param string $url
     *
     * @return void
     */
    public function unsecure($url) {
        if ($this->files->exists($this->certificatesPath($url, 'crt'))) {
            $this->files->unlink($this->nginxPath($url));

            $this->files->unlink($this->certificatesPath($url, 'conf'));
            $this->files->unlink($this->certificatesPath($url, 'key'));
            $this->files->unlink($this->certificatesPath($url, 'csr'));
            $this->files->unlink($this->certificatesPath($url, 'crt'));
        }

        $this->cli->run(sprintf('sudo security delete-certificate -c "%s" /Library/Keychains/System.keychain', $url));
        $this->cli->run(sprintf('sudo security delete-certificate -c "*.%s" /Library/Keychains/System.keychain', $url));
        $this->cli->run(sprintf(
            'sudo security find-certificate -e "%s%s" -a -Z | grep SHA-1 | sudo awk \'{system("security delete-certificate -Z \'$NF\' /Library/Keychains/System.keychain")}\'',
            $url,
            '@cf.devsuite'
        ));
    }

    public function unsecureAll() {
        $tld = $this->config->read()['tld'];

        $secured = $this->parked()
            ->merge($this->links())
            ->sort()
            ->where('secured', ' X');

        if ($secured->count() === 0) {
            return CDevSuite::info('No sites to unsecure. You may list all servable sites or links by running <comment>devsuite:parked</comment> or <comment>devsuite:links</comment>.');
        }

        CDevSuite::info('Attempting to unsecure the following sites:');
        CDevSuite::table(['Site', 'SSL', 'URL', 'Path'], $secured->toArray());

        foreach ($secured->pluck('site') as $url) {
            $this->unsecure($url . '.' . $tld);
        }

        $remaining = $this->parked()
            ->merge($this->links())
            ->sort()
            ->where('secured', ' X');
        if ($remaining->count() > 0) {
            CDevSuite::warning('We were not succesful in unsecuring the following sites:');
            CDevSuite::table(['Site', 'SSL', 'URL', 'Path'], $remaining->toArray());
        }
        CDevSuite::info('unsecure --all was successful.');
    }

    /**
     * Build the Nginx proxy config for the specified domain
     *
     * @param string $url  The domain name to serve
     * @param string $host The URL to proxy to, eg: http://127.0.0.1:8080
     *
     * @return string
     */
    public function proxyCreate($url, $host) {
        if (!preg_match('~^https?://.*$~', $host)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid URL', $host));
        }

        $tld = $this->config->read()['tld'];
        if (!cstr::endsWith($url, '.' . $tld)) {
            $url .= '.' . $tld;
        }

        $siteConf = $this->files->get(CDevSuite::stubsPath() . 'proxy.devsuite.conf');

        $siteConf = str_replace(
            ['DEVSUITE_HOME_PATH', 'DEVSUITE_SERVER_PATH', 'DEVSUITE_STATIC_PREFIX', 'DEVSUITE_SITE', 'DEVSUITE_PROXY_HOST'],
            [rtrim($this->devSuiteHomePath(), '/'), CDevSuite::serverPath(), CDevSuite::staticPrefix(), $url, $host],
            $siteConf
        );

        $this->secure($url, $siteConf);

        CDevSuite::info('DevSuite will now proxy [https://' . $url . '] traffic to [' . $host . '].');
    }

    /**
     * Unsecure the given URL so that it will use HTTP again.
     *
     * @param string $url
     *
     * @return void
     */
    public function proxyDelete($url) {
        $tld = $this->config->read()['tld'];
        if (!cstr::endsWith($url, '.' . $tld)) {
            $url .= '.' . $tld;
        }

        $this->unsecure($url);
        $this->files->unlink($this->nginxPath($url));

        CDevSuite::info('DevSuite will no longer proxy [https://' . $url . '].');
    }

    /**
     * Remove old loopback interface alias and add a new one if necessary.
     *
     * @param string $oldLoopback
     * @param string $loopback
     *
     * @return void
     */
    public function aliasLoopback($oldLoopback, $loopback) {
        if ($oldLoopback !== CDevSuite::loopback()) {
            $this->removeLoopbackAlias($oldLoopback);
        }

        if ($loopback !== CDevSuite::loopback()) {
            $this->addLoopbackAlias($loopback);
        }

        $this->updateLoopbackPlist($loopback);
    }

    /**
     * Remove loopback interface alias.
     *
     * @param string $loopback
     *
     * @return void
     */
    public function removeLoopbackAlias($loopback) {
        $this->cli->run(sprintf(
            'sudo ifconfig lo0 -alias %s',
            $loopback
        ));

        CDevsuite::info('[' . $loopback . '] loopback interface alias removed.');
    }

    /**
     * Add loopback interface alias.
     *
     * @param string $loopback
     *
     * @return void
     */
    public function addLoopbackAlias($loopback) {
        $this->cli->run(sprintf(
            'sudo ifconfig lo0 alias %s',
            $loopback
        ));

        CDevsuite::info('[' . $loopback . '] loopback interface alias added.');
    }

    /**
     * Remove old LaunchDaemon and create a new one if necessary.
     *
     * @param string $loopback
     *
     * @return void
     */
    public function updateLoopbackPlist($loopback) {
        $this->removeLoopbackPlist();

        if ($loopback !== CDevSuite::loopback()) {
            $this->files->put(
                $this->plistPath(),
                str_replace(
                    'DEVSUITE_LOOPBACK',
                    $loopback,
                    $this->files->get(CDevSuite::stubsPath() . 'loopback.plist')
                )
            );

            CDevSuite::info('[' . $this->plistPath() . '] persistent loopback interface alias launch daemon added.');
        }
    }

    /**
     * Remove loopback interface alias launch daemon plist file.
     *
     * @return void
     */
    public function removeLoopbackPlist() {
        if ($this->files->exists($this->plistPath())) {
            $this->files->unlink($this->plistPath());

            CDevsuite::info('[' . $this->plistPath() . '] persistent loopback interface alias launch daemon removed.');
        }
    }

    /**
     * Remove loopback interface alias and launch daemon plist file for uninstall purpose.
     *
     * @return void
     */
    public function uninstallLoopback() {
        if (($loopback = $this->devsuiteLoopback()) !== CDevSuite::loopback()) {
            $this->removeLoopbackAlias($loopback);
        }

        $this->removeLoopbackPlist();
    }

    public function devsuiteLoopback() {
        return $this->config->read()['loopback'];
    }

    /**
     * Get the path to loopback LaunchDaemon.
     *
     * @return string
     */
    public function plistPath() {
        return '/Library/LaunchDaemons/com.cresenity.devsuite.loopback.plist';
    }
}
