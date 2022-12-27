<?php

class CVendor_Cloudflare {
    public static function api($email, $apiKey) {
        return new CVendor_Cloudflare_Api($email, $apiKey);
    }
}
