<?php

/**
 * Trait Create
 *
 * @category Trait
 * @package  Xendit\ApiOperations
 *
 * @author   Ellen <ellen@xendit.co>
 * @license  https://opensource.org/licenses/MIT MIT License
 *
 * @link     https://api.xendit.co
 */
trait CVendor_Xendit_ApiOperation_Create {
    /**
     * Send a create request
     *
     * @param array $params user's params
     *
     * @return array
     */
    public static function create($params = []) {
        self::validateParams($params, static::createReqParams());

        $url = static::classUrl();

        return static::_request('POST', $url, $params);
    }
}
