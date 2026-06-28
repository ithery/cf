<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_LetsEncrypt {
    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @var string
     */
    protected $docRoot;

    /**
     * @param null|CServer_Server $server
     * @param null|string         $docRoot
     */
    public function __construct($server = null, $docRoot = null) {
        $this->server = $server ?: new CServer_Server();
        if ($docRoot !== null) {
            $this->docRoot = rtrim($docRoot, '/');
        } elseif ($this->server->isLocal() && defined('DOCROOT')) {
            $this->docRoot = rtrim(DOCROOT, '/');
        } else {
            $this->docRoot = '';
        }
    }

    /**
     * @return bool
     */
    public function isInstalled() {
        $output = trim($this->server->runCommand('which certbot 2>/dev/null || which letsencrypt 2>/dev/null'));

        return strlen($output) > 0 && strpos($output, 'not found') === false;
    }

    /**
     * @param string $domain
     * @param string $private
     * @param string $public
     * @param string $certificate
     * @param string $fullchain
     *
     * @return array
     */
    public function addCertificate($domain, $private, $public, $certificate, $fullchain) {
        if (empty($domain)) {
            throw new Exception('Domain required');
        }
        if (empty($private)) {
            throw new Exception('Private Key required');
        }
        if (empty($public)) {
            throw new Exception('Public Key required');
        }
        if (empty($certificate)) {
            throw new Exception('Certificate required');
        }
        if (empty($fullchain)) {
            throw new Exception('Fullchain required');
        }

        $certDirectory = $this->docRoot . '/certificate/letsencrypt/' . $domain;
        $this->ensureDirectory($certDirectory);

        $this->writeFile($certDirectory . '/private.pem', $private);
        $this->writeFile($certDirectory . '/public.pem', $public);
        $this->writeFile($certDirectory . '/certificate.crt', $certificate);
        $this->writeFile($certDirectory . '/fullchain.crt', $fullchain);

        $files = [
            $certDirectory . '/private.pem',
            $certDirectory . '/public.pem',
            $certDirectory . '/certificate.crt',
            $certDirectory . '/fullchain.crt',
        ];
        foreach ($files as $file) {
            if (!$this->server->fileExists($file)) {
                throw new Exception('Failed to write certificate file: ' . basename($file));
            }
        }

        return ['path' => $certDirectory];
    }

    /**
     * @param string $filename
     * @param string $content
     *
     * @return void
     */
    public function addChallenge($filename, $content) {
        if (empty($filename)) {
            throw new Exception('Filename required');
        }
        if (empty($content)) {
            throw new Exception('Content required');
        }

        $folder = $this->docRoot . '/.well-known/acme-challenge';
        $this->ensureDirectory($folder);
        $this->writeFile($folder . '/' . $filename, $content);

        if (!$this->server->fileExists($folder . '/' . $filename)) {
            throw new Exception('Failed to write challenge file');
        }
    }

    /**
     * @param string $path
     *
     * @return void
     */
    private function ensureDirectory($path) {
        if ($this->server->isLocal()) {
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            return;
        }
        $this->server->runCommand('mkdir -p ' . escapeshellarg($path));
    }

    /**
     * @param string $filePath
     * @param string $content
     *
     * @return void
     */
    private function writeFile($filePath, $content) {
        if ($this->server->isLocal()) {
            file_put_contents($filePath, $content);

            return;
        }
        $encoded = base64_encode($content);
        $escapedPath = addcslashes($filePath, "'\\");
        $this->server->runCommand(
            "php -r \"file_put_contents('" . $escapedPath . "', base64_decode('" . $encoded . "'));\""
        );
    }
}
