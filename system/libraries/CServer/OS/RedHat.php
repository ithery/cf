<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * RHEL dan turunannya: CentOS, Fedora, Rocky, AlmaLinux.
 */
class CServer_OS_RedHat extends CServer_OSAbstract {
    /**
     * @return string
     */
    public function getFamily() {
        return 'rhel';
    }

    /**
     * DNF menggantikan YUM sejak RHEL 8; CentOS 7 masih memakai YUM.
     *
     * @return string
     */
    public function getPackageManager() {
        $version = (int) $this->getVersion();
        if ($version > 0 && $version < 8) {
            return 'yum';
        }

        return 'dnf';
    }

    /**
     * @param string $package
     *
     * @return array
     */
    public function getInstallCommand($package) {
        return [$this->getPackageManager() . ' install -y ' . $package];
    }

    /**
     * @return array
     */
    protected function serviceUnitMap() {
        return [
            'apache' => 'httpd',
            'redis' => 'redis',
            'mysql' => 'mysqld',
            'cron' => 'crond',
        ];
    }

    /**
     * @return array
     */
    protected function configPathMap() {
        return [
            'redis' => ['/etc/redis.conf', '/etc/redis/redis.conf'],
            'nginx' => ['/etc/nginx/nginx.conf'],
            'apache' => ['/etc/httpd/conf/httpd.conf'],
            'php' => ['/etc/php.d', '/etc/php.ini'],
        ];
    }
}
