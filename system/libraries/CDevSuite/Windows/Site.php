<?php

/**
 * Description of Site.
 *
 * @author Hery
 */
use phpseclib\Crypt\RSA;
use phpseclib\File\X509;

class CDevSuite_Windows_Site extends CDevSuite_Site {
    /**
     * Get the path to Nginx site configuration files.
     *
     * @param null|string $additionalPath
     */
    public function nginxPath($additionalPath = null) {
        if ($additionalPath && !cstr::endsWith($additionalPath, '.conf')) {
            $additionalPath = $additionalPath . '.conf';
        }

        return $this->devSuiteHomePath() . '/Nginx' . ($additionalPath ? '/' . $additionalPath : '');
    }

    /**
     * Get all certificates from config folder.
     *
     * @param string $path
     *
     * @return \CCollection
     */
    public function getCertificates($path = null) {
        $path = $path ?: $this->certificatesPath();

        $this->files->ensureDirExists($path, CDevsuite::user());

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
            $this->unsecure($url);
        }

        foreach ($secured as $url) {
            $this->secure(str_replace('.' . $oldTld, '.' . $tld, $url));
        }
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

        $this->files->ensureDirExists($this->certificatesPath(), CDevsuite::user());

        $this->files->ensureDirExists($this->nginxPath(), CDevsuite::user());

        $this->createCertificate($url);

        $this->files->putAsUser(
            $this->nginxPath($url),
            $this->buildSecureNginxServer($url, $siteConf)
        );
    }

    /**
     * Get the port of the given host.
     *
     * @param string $url
     *
     * @return int
     */
    public function port($url) {
        if ($this->files->exists($path = $this->nginxPath($url))) {
            if (strpos($this->files->get($path), '443') !== false) {
                return 443;
            }
        }

        return 80;
    }

    /**
     * Create and trust a certificate for the given URL.
     *
     * @param string $url
     *
     * @return void
     */
    public function createCertificate($url) {
        $keyPath = $this->certificatesPath() . '/' . $url . '.key';
        $csrPath = $this->certificatesPath() . '/' . $url . '.csr';
        $crtPath = $this->certificatesPath() . '/' . $url . '.crt';

        $this->createPrivateKey($keyPath);
        $this->createSigningRequest($url, $keyPath, $csrPath);
        $this->createSignedCertificate($keyPath, $csrPath, $crtPath);

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
        $key = (new RSA())->createKey(2048);

        $this->files->putAsUser($keyPath, $key['privatekey']);
    }

    /**
     * Create the signing request for the TLS certificate.
     *
     * @param string $keyPath
     * @param mixed  $url
     * @param mixed  $csrPath
     *
     * @return void
     */
    public function createSigningRequest($url, $keyPath, $csrPath) {
        $privKey = new RSA();
        $privKey->loadKey($this->files->get($keyPath));

        $x509 = new X509();
        $x509->setPrivateKey($privKey);
        $x509->setDNProp('commonname', $url);

        $x509->loadCSR($x509->saveCSR($x509->signCSR()));

        $x509->setExtension('id-ce-subjectAltName', [
            ['dNSName' => $url],
            ['dNSName' => "*.$url"],
        ]);

        $x509->setExtension('id-ce-keyUsage', [
            'digitalSignature',
            'nonRepudiation',
            'keyEncipherment',
        ]);

        $csr = $x509->saveCSR($x509->signCSR());

        $this->files->putAsUser($csrPath, $csr);
    }

    /**
     * Create the signed TLS certificate.
     *
     * @param string $keyPath
     * @param string $csrPath
     * @param string $crtPath
     *
     * @return void
     */
    public function createSignedCertificate($keyPath, $csrPath, $crtPath) {
        $privKey = new RSA();
        $privKey->loadKey($this->files->get($keyPath));

        $subject = new X509();
        $subject->loadCSR($this->files->get($csrPath));

        $issuer = new X509();
        $issuer->setPrivateKey($privKey);
        $issuer->setDN($subject->getDN());

        $x509 = new X509();
        $x509->makeCA();
        $x509->setStartDate('-1 day');

        $result = $x509->sign($issuer, $subject, 'sha256WithRSAEncryption');
        $certificate = $x509->saveX509($result);

        $this->files->putAsUser($crtPath, $certificate);
    }

    /**
     * Trust the given certificate file in the Mac Keychain.
     *
     * @param string     $crtPath
     * @param null|mixed $url
     *
     * @return void
     */
    public function trustCertificate($crtPath, $url = null) {
        CDevSuite::info('Trust Certificate ' . $crtPath);
        $this->cli->run(sprintf('cmd "/C certutil -addstore "Root" "%s""', $crtPath));
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

        return str_replace(
            ['DEVSUITE_HOME_PATH', 'DEVSUITE_SERVER_PATH', 'DEVSUITE_STATIC_PREFIX', 'DEVSUITE_SITE', 'DEVSUITE_CERT', 'DEVSUITE_KEY', 'HOME_PATH'],
            [CDevSuite::homePath(), CDevSuite::serverPath(), CDevSuite::staticPrefix(), $url, $path . '/' . $url . '.crt', $path . '/' . $url . '.key', $_SERVER['HOME']],
            $this->files->get(CDevSuite::stubsPath() . 'secure.devsuite.conf')
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
        if ($this->files->exists($this->certificatesPath() . '/' . $url . '.crt')) {
            $this->files->unlink(CDevSuite::homePath() . "/Nginx/$url.conf");

            $this->files->unlink($this->certificatesPath() . '/' . $url . '.key');
            $this->files->unlink($this->certificatesPath() . '/' . $url . '.csr');
            $this->files->unlink($this->certificatesPath() . '/' . $url . '.crt');

            $this->cli->run(sprintf('cmd "/C certutil -delstore "Root" "%s""', $url));
        }
    }
}
