<?php

use Discord\Discord;
use React\EventLoop\LoopInterface;

class CBot_Driver_DiscordDriver_Factory {
    /**
     * Create a new Bot instance.
     *
     * @param array                          $config
     * @param LoopInterface                  $loop
     * @param CCache_Repository              $cache
     * @param CBot_Contract_StorageInterface $storageDriver
     *
     * @return \CBot_Bot
     */
    public static function createForDiscord(
        array $config,
        LoopInterface $loop,
        CCache_Repository $cache = null,
        CBot_Contract_StorageInterface $storageDriver = null
    ) {
        $client = new Discord([
            'token' => CCollection::make($config['discord'])->get('token'),
            'loop' => $loop,
        ]);

        return static::createUsingDiscord($config, $client, $cache, $storageDriver);
    }

    /**
     * Create a new Bot instance.
     *
     * @param array                          $config
     * @param Discord                        $client
     * @param CCache_Repository              $cache
     * @param CBot_Contract_StorageInterface $storageDriver
     *
     * @return CBot_Bot
     *
     * @internal param LoopInterface $loop
     */
    public static function createUsingDiscord(
        array $config,
        Discord $client,
        CCache_Repository $cache = null,
        CBot_Contract_StorageInterface $storageDriver = null
    ) {
        if (empty($cache)) {
            $cache = CCache::manager()->driver('array');
        }

        if (empty($storageDriver)) {
            $storageDriver = CBot_Factory::createDefaultFileStorage(CBot_Driver_DiscordDriver::getName());
        }

        $driver = new CBot_Driver_DiscordDriver($config, $client);
        $bot = new CBot_Bot($config, $driver, $storageDriver, $cache);

        $client->on('message', function () use ($bot) {
            $bot->listen();
        });

        $client->on('ready', function ($discord) use ($driver) {
            $driver->connected();
        });

        return $bot;
    }
}
