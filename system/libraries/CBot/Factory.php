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
        $driver = null;
        $driverName = carr::get($config, 'driver');

        if ($driverName) {
            $driver = $driverManager->getDriverFor($driverName, $request);
        }
        if ($driver == null) {
            $driver = $driverManager->getMatchingDriver($request);
        }

        if ($storage == null) {
            $storage = static::createDefaultFileStorage();
        }
        if ($cache == null) {
            $cache = CCache::manager()->driver('file');
        }

        return new CBot_Bot($config, $driver, $storage, $cache);
    }

    /**
     * @param null|string $driverName
     *
     * @return CBot_Storage_FileStorage
     */
    public static function createDefaultFileStorage($driverName = null) {
        $dir = DOCROOT . 'temp' . DS . 'bot' . DS;
        if ($driverName) {
            $dir .= $driverName . DS;
        }

        return new CBot_Storage_FileStorage($dir);
    }

    /**
     * @return CCache_Repository
     */
    public static function getDefaultArrayCache() {
        return CCache::manager()->driver('array');
    }

    public static function createForDiscord(
        array $config,
        LoopInterface $loop,
        CCache_Repository $cache = null,
        CBot_Contract_StorageInterface $storageDriver = null
    ) {
        return CBot_Driver_DiscordDriver_Factory::createForDiscord($config, $loop, $cache, $storageDriver);
    }
}
