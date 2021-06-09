<?php

/**
 * Trait Update
 *
 * @category Trait
 * @package  Xendit\ApiOperations
 *
 * @author   Ellen <ellen@xendit.co>
 * @license  https://opensource.org/licenses/MIT MIT License
 *
 * @link     https://api.xendit.co
 */
trait CVendor_Xendit_ApiOperation_Update {
    /**
     * Send an update request
     *
     * @param string $id     data ID
     * @param array  $params user's params
     *
     * @return array
     */
    public static function update($id, $params = []) {
        self::validateParams($params, static::updateReqParams());

        $url = static::classUrl() . '/' . $id;

        return static::_request('PATCH', $url, $params);
    }
}
