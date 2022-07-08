<?php

class CBot {
    /**
     * @param array $config
     *
     * @return CBot_Bot
     */
    public static function createBot(array $config = []) {
        return CBot_Factory::create($config);
    }
}
