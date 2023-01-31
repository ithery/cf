<?php

class CVendor_Discord {
    public static function webhook($url) {
        return new CVendor_Discord_Webhook($url);
    }
}
