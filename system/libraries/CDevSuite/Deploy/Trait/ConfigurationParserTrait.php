<?php

/**
 * Description of ConfigurationParserTrait.
 *
 * @author Hery
 */
trait CDevSuite_Deploy_Trait_ConfigurationParserTrait {
    /**
     * Get the configured server from the SSH config.
     *
     * @param string $host
     *
     * @return null|string
     */
    protected function getConfiguredServer($host) {
        if ($config = $this->getSshConfig($this->getSystemUser())) {
            return $config->findConfiguredHost($host);
        }
    }

    /**
     * Get the SSH configuration file instance.
     *
     * @param string $user
     *
     * @return null|\CDevSuite_Deploy_SSHConfigFile
     */
    protected function getSshConfig($user) {
        if (file_exists($path = $this->getHomeDirectory($user) . '/.ssh/config')) {
            return CDevSuite_Deploy_SSHConfigFile::parse($path);
        }
    }

    /**
     * Get the home directory for the user based on OS.
     *
     * @param string $user
     *
     * @return null|string
     */
    protected function getHomeDirectory($user) {
        $system = strtolower(php_uname());

        if (cstr::contains($system, 'darwin')) {
            return "/Users/{$user}";
        } elseif (cstr::contains($system, 'windows')) {
            return "C:\\Users\\{$user}";
        } elseif (cstr::contains($system, 'linux')) {
            return "/home/{$user}";
        }
    }

    /**
     * Get the system user.
     *
     * @return string
     */
    protected function getSystemUser() {
        if (cstr::contains(strtolower(php_uname()), 'windows')) {
            return getenv('USERNAME');
        }

        return posix_getpwuid(posix_geteuid())['name'];
    }

    /**
     * Determine if the given value is a valid IP.
     *
     * @param string $value
     *
     * @return bool
     */
    protected function isValidIp($value) {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }
}
