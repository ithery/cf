<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 16, 2019, 1:20:03 AM
 */
use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as LoopFactory;

class CDaemon_Loop {
    /**
     * @return \React\EventLoop\LoopInterface
     */
    public static function reactFactory() {
        return new CDaemon_Loop_ReactFactory();
    }
}
