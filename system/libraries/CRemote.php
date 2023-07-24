<?php

defined('SYSPATH') or die('No direct access allowed.');

class CRemote {
    /**
     * @param array $config
     *
     * @return CRemote_SSH
     */
    public static function ssh($config) {
        return new CRemote_SSH($config);
    }
}
