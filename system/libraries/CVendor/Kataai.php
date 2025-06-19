<?php

class CVendor_Kataai {
    public static function api($options = []) {
        $client = new CVendor_Kataai_Client($options);

        return new CVendor_Kataai_Api($client);
    }

    public static function getBaseUrl() {
        return 'https://api-whatsapp.kata.ai';
    }

    /**
     * @return CVendor_Kataai_Message
     */
    public static function createMessage() {
        return new CVendor_Kataai_Message();
    }
}
