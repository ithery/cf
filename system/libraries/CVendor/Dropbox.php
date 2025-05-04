<?php

class CVendor_Dropbox {
    /**
     * Creates and returns an instance of CVendor_Dropbox_Client.
     *
     * @param array $options An array of options to configure the client, including:
     *                       - 'clientId': The client ID for Dropbox (optional).
     *                       - 'clientSecret': The client secret for Dropbox (optional).
     *                       Defaults are retrieved from the configuration if not provided.
     *
     * @return CVendor_Dropbox_Client the configured Dropbox client instance
     */
    public static function client($options = []) {
        $clientId = carr::get($options, 'clientId', CF::config('vendor.dropbox.client_id'));
        $clientSecret = carr::get($options, 'clientSecret', CF::config('vendor.dropbox.client_secret'));
        $client = new CVendor_Dropbox_Client($clientId, $clientSecret, $options);

        return $client;
    }

    public static function connect($options = []) {
        $client = self::client($options);

        return $client->connect();
    }
}
