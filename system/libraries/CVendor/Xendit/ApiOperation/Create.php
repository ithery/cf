<?php

trait CVendor_Xendit_ApiOperation_Create {
    /**
     * Send a create request
     *
     * @param array $params user's params
     *
     * @return array
     */
    public function create($params = []) {
        $this->validateParams($params, $this->createReqParams());

        $url = $this->classUrl();

        return $this->request('POST', $url, $params);
    }
}
