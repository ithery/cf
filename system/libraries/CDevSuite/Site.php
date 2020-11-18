<?php

/**
 * Description of Site
 *
 * @author Hery
 */
abstract class CDevSuite_Site {

    /**
     *
     * @var CDevSuite_Windows_Configuration
     */
    public $config;

    /**
     *
     * @var CDevSuite_Windows_CommandLine
     */
    public $cli;

    /**
     *
     * @var CDevSuite_Windows_Filesystem
     */
    public $files;

    /**
     * Create a new Site instance.
     *
     * @param Configuration $config
     * @param CommandLine   $cli
     * @param Filesystem    $files
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
        $this->files = CDevSuite::filesystem();
        $this->config = CDevSuite::configuration();
    }

    public function devSuiteHomePath() {
        return CDevSuite::homePath();
    }

    /**
     * Get the path to the linked DevSuite sites.
     *
     * @return string
     */
    public function sitesPath($link = null) {
        return $this->devSuiteHomePath() . 'Sites' . ($link ? '/' . $link : '');
    }

    /**
     * Get the path to the DevSuite CA certificates.
     *
     * @return string
     */
    public function caPath($caFile = null) {
        return $this->devSuiteHomePath() . 'CA' . ($caFile ? '/' . $caFile : '');
    }

    /**
     * Get the path to the DevSuite TLS certificates.
     *
     * @return string
     */
    public function certificatesPath($url = null, $extension = null) {
        $url = $url ? '/' . $url : '';
        $extension = $extension ? '.' . $extension : '';

        return $this->devSuiteHomePath() . 'Certificates' . $url . $extension;
    }

    /**
     * Get the path to Nginx site configuration files.
     */
    public function nginxPath($additionalPath = null) {
        return $this->devSuiteHomePath() . 'Nginx' . ($additionalPath ? '/' . $additionalPath : '');
    }

    /**
     * Link the current working directory with the given name.
     *
     * @param string $target
     * @param string $link
     * @return string
     */
    public function link($target, $link) {
        $this->files->ensureDirExists(
                $linkPath = $this->sitesPath(), CDevSuite::user()
        );

        $this->unlink($link);

        $this->config->prependPath($linkPath);

        $this->files->symlinkAsUser($target, $linkPath . '/' . $link);

        return $linkPath . '/' . $link;
    }

    /**
     * Get link name based on the current directory.
     *
     * @return null|string
     */
    private function getLinkNameByCurrentDir() {
        $count = count($links = $this->links()->where('path', getcwd()));

        if ($count == 1) {
            return $links->shift()['site'];
        }

        if ($count > 1) {
            throw new DomainException("There are {$count} links related to the current directory, please specify the name: devsuite:unlink <name>.");
        }
    }

    /**
     * Get the name of the site.
     *
     * @param  string|null $name
     * @return string
     */
    private function getRealSiteName($name) {
        if (!is_null($name)) {
            return $name;
        }

        if (is_string($link = $this->getLinkNameByCurrentDir())) {
            return $link;
        }

        return basename(getcwd());
    }

    /**
     * Unlink the given symbolic link.
     *
     * @param  string  $name
     * @return void
     */
    public function unlink($name) {
        $name = $this->getRealSiteName($name);

        
        if ($this->files->exists($path = $this->sitesPath($name))) {
            $this->files->unlink($path);
        }

        return $name;
    }

    /**
     * Get the real hostname for the given path, checking links.
     *
     * @param  string  $path
     * @return string|null
     */
    public function host($path) {
        foreach ($this->files->scandir($this->sitesPath()) as $link) {
            if ($resolved = realpath($this->sitesPath($link)) === $path) {
                return $link;
            }
        }

        return basename($path);
    }

    /**
     * Pretty print out all links in DevSuite.
     *
     * @return CCollection
     */
    public function links() {
        $certsPath = $this->certificatesPath();

        $this->files->ensureDirExists($certsPath, CDevSuite::user());

        $certs = $this->getCertificates($certsPath);

        return $this->getSites($this->sitesPath(), $certs);
    }

    /**
     * Pretty print out all parked links in DevSuite
     *
     * @return CCollection
     */
    public function parked() {
        $certs = $this->getCertificates();

        $links = $this->getSites($this->sitesPath(), $certs);

        $config = $this->config->read();
        $parkedLinks = c::collect();
        foreach (array_reverse($config['paths']) as $path) {
            if ($path === $this->sitesPath()) {
                continue;
            }

            // Only merge on the parked sites that don't interfere with the linked sites
            $sites = $this->getSites($path, $certs)->filter(function ($site, $key) use ($links) {
                return !$links->has($key);
            });

            $parkedLinks = $parkedLinks->merge($sites);
        }

        return $parkedLinks;
    }

    /**
     * Get all sites which are proxies (not Links, and contain proxy_pass directive)
     *
     * @return CCollection
     */
    public function proxies() {
        $dir = $this->nginxPath();
        $tld = $this->config->read()['tld'];
        $links = $this->links();
        $certs = $this->getCertificates();

        if (!$this->files->exists($dir)) {
            return c::collect();
        }

        $proxies = c::collect($this->files->scandir($dir))
                        ->filter(function ($site, $key) use ($tld) {
                            // keep sites that match our TLD
                            return cstr::endsWith($site, '.' . $tld);
                        })->map(function ($site, $key) use ($tld) {
                    // remove the TLD suffix for consistency
                    return str_replace('.' . $tld, '', $site);
                })->reject(function ($site, $key) use ($links) {
                    return $links->has($site);
                })->mapWithKeys(function ($site) {
                    $host = $this->getProxyHostForSite($site) ? : '(other)';
                    return [$site => $host];
                })->reject(function ($host, $site) {
                    // If proxy host is null, it may be just a normal SSL stub, or something else; either way we exclude it from the list
                    return $host === '(other)';
                })->map(function ($host, $site) use ($certs, $tld) {
            $secured = $certs->has($site);
            $url = ($secured ? 'https' : 'http') . '://' . $site . '.' . $tld;

            return [
                'site' => $site,
                'secured' => $secured ? ' X' : '',
                'url' => $url,
                'path' => $host,
            ];
        });

        return $proxies;
    }

    /**
     * Identify whether a site is for a proxy by reading the host name from its config file.
     *
     * @param string $site Site name without TLD
     * @param string $configContents Config file contents
     * @return string|null
     */
    public function getProxyHostForSite($site, $configContents = null) {
        $siteConf = $configContents ? : $this->getSiteConfigFileContents($site);

        if (empty($siteConf)) {
            return null;
        }

        $host = null;
        if (preg_match('~proxy_pass\s+(?<host>https?://.*)\s*;~', $siteConf, $patterns)) {
            $host = trim($patterns['host']);
        }
        return $host;
    }

    public function getSiteConfigFileContents($site, $suffix = null) {
        $config = $this->config->read();
        $suffix = $suffix ? : '.' . $config['tld'];
        $file = str_replace($suffix, '', $site) . $suffix;
        return $this->files->exists($this->nginxPath($file)) ? $this->files->get($this->nginxPath($file)) : null;
    }

    /**
     * @deprecated Use getSites instead which works for both normal and symlinked paths.
     *
     * @param string $path
     * @param CCollection $certs
     * @return CCollection
     */
    function getLinks($path, $certs) {
        return $this->getSites($path, $certs);
    }

    /**
     * Get list of sites and return them formatted
     * Will work for symlink and normal site paths
     *
     * @param $path
     * @param $certs
     *
     * @return CCollection
     */
    function getSites($path, $certs) {
        $config = $this->config->read();

        $this->files->ensureDirExists($path, CDevSuite::user());

        return c::collect($this->files->scandir($path))->mapWithKeys(function ($site) use ($path) {
                    $sitePath = $path . '/' . $site;

                    if ($this->files->isLink($sitePath)) {
                        $realPath = $this->files->readLink($sitePath);
                    } else {
                        $realPath = $this->files->realpath($sitePath);
                    }
                    return [$site => $realPath];
                })->filter(function ($path) {
                    return $this->files->isDir($path);
                })->map(function ($path, $site) use ($certs, $config) {
                    $secured = $certs->has($site);
                    $url = ($secured ? 'https' : 'http') . '://' . $site . '.' . $config['tld'];

                    return [
                        'site' => $site,
                        'secured' => $secured ? ' X' : '',
                        'url' => $url,
                        'path' => $path,
                    ];
                });
    }

    /**
     * Remove all broken symbolic links.
     *
     * @return void
     */
    public function pruneLinks() {
        $this->files->ensureDirExists($this->sitesPath(), CDevSuite::user());

        $this->files->removeBrokenLinksAt($this->sitesPath());
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

}
