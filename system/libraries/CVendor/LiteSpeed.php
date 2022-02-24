<?php

class CVendor_LiteSpeed {
    const DEFAULT_SERVER_ROOT = '/usr/local/lsws';

    public static function serverRoot() {
        return static::DEFAULT_SERVER_ROOT;
    }
}
