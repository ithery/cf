<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 16, 2019, 1:54:24 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use React\EventLoop\ExtEvLoop;
use React\EventLoop\ExtUvLoop;
use React\EventLoop\ExtLibevLoop;
use React\EventLoop\ExtLibeventLoop;
use React\EventLoop\ExtEventLoop;
use React\EventLoop\StreamSelectLoop;
use React\EventLoop\Factory;

class CDaemon_Loop_ReactFactory {

    public static function createExtUvLoop() {
        return new ExtUvLoop();
    }

    public static function createExtEvLoop() {
        return new ExtEvLoop();
    }

    public static function createExtLibevLoop() {
        return new ExtLibevLoop();
    }

    public static function createExtLibeventLoop() {
        return new ExtLibeventLoop();
    }

    public static function createExtEventLoop() {
        return new ExtEventLoop();
    }

    public static function createStreamSelectLoop() {
        return new StreamSelectLoop();
    }

    public static function auto() {
        return Factory::create();
    }

}
