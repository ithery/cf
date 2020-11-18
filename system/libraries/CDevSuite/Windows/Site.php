<?php

/**
 * Description of Site
 *
 * @author Hery
 */
use phpseclib\Crypt\RSA;
use phpseclib\File\X509;

class CDevSuite_Windows_Site extends CDevSuite_Site {

    /**
     * Get all certificates from config folder.
     *
     * @param string $path
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCertificates($path) {
        return c::collect($this->files->scanDir($path))->filter(function ($value, $key) {
                    return ends_with($value, '.crt');
                })->map(function ($cert) {
                    $certWithoutSuffix = substr($cert, 0, -4);

                    return substr($certWithoutSuffix, 0, strrpos($certWithoutSuffix, '.'));
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
     * Secure the given host with TLS.
     *
     * @param string $url
     *
     * @return void
     */
    public function secure($url) {
        $this->unsecure($url);

        $this->files->ensureDirExists($this->certificatesPath(), user());

        $this->createCertificate($url);

        $this->files->putAsUser(
                CDevSuite::homePath() . "/Nginx/$url.conf", $this->buildSecureNginxServer($url)
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
        if ($this->files->exists($path = CDevSuite::homePath() . "/Nginx/$url.conf")) {
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
     * @param string $crtPath
     *
     * @return void
     */
    public function trustCertificate($crtPath) {
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
                ['DEVSUITE_HOME_PATH', 'DEVSUITE_SERVER_PATH', 'DEVSUITE_STATIC_PREFIX', 'DEVSUITE_SITE', 'DEVSUITE_CERT', 'DEVSUITE_KEY', 'HOME_PATH'], [CDevSuite::homePath(), CDevSuite::serverPath(), CDevSuite::staticPrefix(), $url, $path . '/' . $url . '.crt', $path . '/' . $url . '.key', $_SERVER['HOME']], $this->files->get(CDevSuite::stubsPath() . 'win/secure.devsuite.conf')
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
