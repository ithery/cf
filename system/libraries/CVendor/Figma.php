<?php

class CVendor_Figma {
    public static function api($accessToken, $options = []) {
        return new CVendor_Figma_Api($accessToken, $options);
    }
}
