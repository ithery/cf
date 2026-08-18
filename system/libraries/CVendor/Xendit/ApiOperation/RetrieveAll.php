<?php

trait CVendor_Xendit_ApiOperation_RetrieveAll {
    /**
     * Send request to get all object, e.g Invoice.
     *
     * @param array $options
     *
     * @return array
     */
    public function retrieveAll($options = []) {
        $url = $this->classUrl();

        return $this->request('GET', $url, $options);
    }
}
