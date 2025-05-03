<?php

class CVendor_Dropbox {
    public function connect($options = []) {
        $clientId = carr::get($options, 'clientId', CF::config('vendor.dropbox.client_id'));
        $clientSecret = carr::get($options, 'clientSecret', CF::config('vendor.dropbox.client_secret'));
        $client = new CVendor_Dropbox_Client($clientId, $clientSecret, $options);

        return $client->connect();
    }
}
