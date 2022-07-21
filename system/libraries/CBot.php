<?php
use React\EventLoop\LoopInterface;

class CBot {
    /**
     * @param array $config
     *
     * @return CBot_Bot
     */
    public static function createBot(array $config = []) {
        return CBot_Factory::create($config);
    }

    /**
     * @param array $config
     *
     * @return CBot_Bot
     */
    public static function createForDiscord(array $config, LoopInterface $loop) {
        return CBot_Factory::createForDiscord($config, $loop);
    }
}
