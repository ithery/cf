<?php

trait CVendor_Xendit_ApiOperation_RetrieveAll {
    /**
     * Send request to get all object, e.g Invoice
     *
     * @return array
     */
    public static function retrieveAll() {
        $url = static::classUrl();
        return static::_request('GET', $url, []);
    }
}
