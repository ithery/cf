<?php
use React\Socket\Server;
use React\EventLoop\LoopInterface;

class CBot_Factory {
    /**
     * Create a new Bot instance.
     *
     * @param array         $config
     * @param CHTTP_Request $request
     * @param null|mixed    $storage
     *
     * @return \CBot_Bot
     */
    public static function create(array $config, CBot_Contract_StorageInterface $storage = null, CCache_Repository $cache = null, CHTTP_Request $request = null) {
        $request = $request ?: c::request();
        $driverManager = new CBot_DriverManager($config);
        $driver = $driverManager->getMatchingDriver($request);
        if ($storage == null) {
            $storage = new CBot_Storage_FileStorage(DOCROOT . 'temp' . DS . 'bot' . DS);
        }
        if ($cache == null) {
            $cache = c::cache()->store();
        }

        return new CBot_Bot($config, $driver, $storage, $cache);
    }
}
