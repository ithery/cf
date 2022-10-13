<?php

class CServer_NodeJs_Installer {
    protected $maxInstallRetry;

    protected $directory;

    protected $modulePaths;

    public function __construct($directory, $modulePaths, $maxInstallRetry) {
        $this->directory = $directory;
        $this->maxInstallRetry = $maxInstallRetry;
        $this->modulePaths = $modulePaths;
    }

    protected function getPackagesList(array $npm) {
        return $npm;
    }

    public function installPackages($npm, $onFound = null) {
        if (!count($npm)) {
            return true;
        }

        $packages = '';
        $packageNames = [];

        foreach ($npm as $package => $version) {
            if (is_int($package)) {
                $package = $version;
                $version = '*';
            }
            if (!$this->isInstalledPackage($package)) {
                $packageNames[] = $package;
                $install = $package . '@"' . addslashes($version) . '"';

                if ($onFound) {
                    call_user_func($onFound, $install);
                }

                $packages .= ' ' . $install;
            }
        }
        if (count($packageNames) > 0) {
            for ($i = $this->maxInstallRetry; $i > 0; $i--) {
                $result = shell_exec(
                    'npm install --force --loglevel=error '
                    . '--prefix ' . escapeshellarg($this->directory)
                    . $packages
                    . ' 2>&1'
                );

                if (strpos($result, 'npm ERR!') === false && $this->isInstalledPackages($packageNames)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function install(array $npm) {
        if (!count($npm)) {
            throw new Exception('No packages found.');
        }
        $result = [];
        $result['message'] = [];
        $result['error'] = false;
        $installed = $this->installPackages($npm, function ($install) use (&$result) {
            $result['message'][] = 'Package added to be installed/updated with npm: ' . $install;
        });
        if ($installed) {
            $result['message'][] = 'Packages installed.';
        } else {
            $result['error'] = true;
            $result['message'][] = 'Installation failed after ' . $this->maxInstallRetry . ' tries.';
        }

        return $result;
    }

    public function isInstalledPackage($package) {
        if (!file_exists($this->getNodeModule($package))) {
            return false;
        }

        return true;
    }

    public function isInstalledPackages($packages) {
        if (!is_array($packages)) {
            $packages = [$packages];
        }

        foreach ($packages as $package) {
            if (!$this->isInstalledPackage($package)) {
                return false;
            }
        }

        return true;
    }

    public function getNodeModule($module) {
        return empty($this->modulePaths[$module])
            ? $this->getNodeModules() . DIRECTORY_SEPARATOR . $module
            : $this->modulePaths[$module];
    }

    public function getNodeModules() {
        return $this->directory . DIRECTORY_SEPARATOR . 'node_modules';
    }
}
