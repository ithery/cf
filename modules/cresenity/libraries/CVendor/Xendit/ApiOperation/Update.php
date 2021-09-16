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
    public function update($id, $params = []) {
        $this->validateParams($params, $this->updateReqParams());

        $url = $this->classUrl() . '/' . $id;

        return $this->request('PATCH', $url, $params);
    }
}
