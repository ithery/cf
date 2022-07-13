<?php

use Discord\Discord;
use BotMan\BotMan\BotMan;
use React\EventLoop\LoopInterface;
use BotMan\BotMan\Cache\ArrayCache;
use BotMan\BotMan\Interfaces\CacheInterface;
use BotMan\BotMan\Interfaces\StorageInterface;
use BotMan\BotMan\Storages\Drivers\FileStorage;

class CBot_Driver_DiscordDriver_Factory {
    /**
     * Create a new BotMan instance.
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
     * Create a new BotMan instance.
     *
     * @param array                          $config
     * @param Discord                        $client
     * @param CacheInterface                 $cache
     * @param CBot_Contract_StorageInterface $storageDriver
     *
     * @return BotMan
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
            $storageDriver = new CBot_Contract_StorageInterface(__DIR__);
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
