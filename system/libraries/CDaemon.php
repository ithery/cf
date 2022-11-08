<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @see CDaemon_Runner
 * @since Mar 12, 2019, 3:17:44 PM
 */
class CDaemon {
    const COMMAND_START = 'start';

    const COMMAND_STOP = 'stop';

    const COMMAND_DEBUG = 'debug';

    const COMMAND_RESTART = 'restart';

    const COMMAND_KILL = 'kill';

    const COMMAND_STATUS = 'status';

    const COMMAND_RELOAD = 'reload';

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var CDaemon_Helper
     */
    protected $helper;

    /**
     * @var CDaemon_ServiceAbstract
     */
    protected static $runningService = null;

    public static function cliRunner($parameter = null) {
        $argv = carr::get($_SERVER, 'argv');
        if ($parameter == null) {
            $parameter = $argv[3];
        }
        parse_str($parameter, $config);
        $cls = carr::get($config, 'serviceClass');
        self::$runningService = CDaemon_Manager::createService($cls);
        CDaemon_ErrorHandler::init();
        self::$runningService->start();
    }

    /**
     * @return CDaemon_ServiceAbstract
     */
    public static function getRunningService() {
        return self::$runningService;
    }

    /**
     * @return CDaemon_Factory
     */
    public static function factory() {
        return CDaemon_Factory::instance();
    }

    /**
     * Shortcut function to log the current running service.
     *
     * @param string $msg
     * @param string $label
     */
    public static function log($msg, $label = '') {
        $runningService = self::getRunningService();
        if ($runningService != null) {
            $runningService->log($msg, $label);
        }
    }

    public static function createRunner($serviceClass, $domain = null) {
        return new CDaemon_Runner($serviceClass, $domain);
    }

    public static function isDaemon() {
        return self::getRunningService() != null;
    }

    /**
     * @return CDaemon_Supervisor
     */
    public static function supervisor() {
        return new CBase_ForwarderStaticClass(CDaemon_Supervisor::class);
    }
}
