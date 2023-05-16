<?php

class CVendor_BunnyCDN_ApiFactory {
    public static function streamApi($streamAccessKey, $libraryId = null) {
        return new CVendor_BunnyCDN_Api_StreamApi(new CVendor_BunnyCDN_Client_StreamClient($streamAccessKey), $libraryId);
    }
}
