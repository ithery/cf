<?php

class CVendor_Qontak {
    /**
     * @param array $options
     *
     * @return CVendor_Qontak_Client
     */
    public static function api($options = []) {
        return CVendor_Qontak_ClientFactory::makeFromArray($options);
    }
}
