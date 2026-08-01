<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Debian dan turunannya, termasuk Ubuntu.
 */
class CServer_OS_Debian extends CServer_OSAbstract {
    /**
     * @return string
     */
    public function getFamily() {
        return 'debian';
    }

    /**
     * @return string
     */
    public function getPackageManager() {
        return 'apt';
    }

    /**
     * @param string $package
     *
     * @return array
     */
    public function getInstallCommand($package) {
        return [
            'apt-get update -y',
            'DEBIAN_FRONTEND=noninteractive apt-get install -y ' . $package,
        ];
    }

    /**
     * @return array
     */
    protected function serviceUnitMap() {
        return [
            'apache' => 'apache2',
            'redis' => 'redis-server',
            'mysql' => 'mysql',
            'cron' => 'cron',
        ];
    }

    /**
     * @return array
     */
    protected function configPathMap() {
        return [
            'redis' => ['/etc/redis/redis.conf'],
            'nginx' => ['/etc/nginx/nginx.conf'],
            'apache' => ['/etc/apache2/apache2.conf'],
            'php' => ['/etc/php'],
        ];
    }
}
