<?php

trait CVendor_Xendit_ApiOperation_RetrieveAll {
    /**
     * Send request to get all object, e.g Invoice
     *
     * @return array
     */
    public function retrieveAll() {
        $url = $this->classUrl();
        return $this->request('GET', $url, []);
    }
}
