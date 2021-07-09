<?php

trait CVendor_Xendit_ApiOperation_Retrieve {
    /**
     * Send GET request to retrieve data
     *
     * @param string|null $id ID
     *
     * @return array
     */
    public static function retrieve($id) {
        $url = static::classUrl() . '/' . $id;
        return static::_request('GET', $url, []);
    }
}
