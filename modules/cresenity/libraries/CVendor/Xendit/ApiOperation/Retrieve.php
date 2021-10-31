<?php

trait CVendor_Xendit_ApiOperation_Retrieve {
    /**
     * Send GET request to retrieve data
     *
     * @param string|null $id ID
     *
     * @return array
     */
    public function retrieve($id) {
        $url = $this->classUrl() . '/' . $id;
        return $this->request('GET', $url, []);
    }
}
