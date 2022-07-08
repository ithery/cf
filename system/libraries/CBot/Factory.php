<?php
use React\Socket\Server;
use React\EventLoop\LoopInterface;

class CBot_Factory {
    /**
     * Create a new Bot instance.
     *
     * @param array         $config
     * @param CHTTP_Request $request
     *
     * @return \CBot_Bot
     */
    public static function create(array $config, CHTTP_Request $request = null) {
        $driverManager = new CBot_DriverManager($config);
        $driver = $driverManager->getMatchingDriver($request);

        return new CBot_Bot($config, $driver);
    }
}
