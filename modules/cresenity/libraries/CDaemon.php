<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 12, 2019, 3:17:44 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

class CDaemon {
        public static function cliRunner($parameter = null) {

        $argv = carr::get($_SERVER, 'argv');
        if ($parameter == null) {
            $parameter = $argv[3];
        }
        parse_str($parameter, $config);
        $cls = carr::get($config, 'serviceClass');
        /** @var CJob_Exception $job */
        $serviceName = carr::get($config, 'serviceName');

        $service = new $cls($serviceName, $config);
        $service->run();
    }

}