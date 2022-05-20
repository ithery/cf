<?php

class CVendor_Midtrans {
    public function __construct($options) {
        $serverKey = carr::get($options, 'serverKey');
        $clientKey = carr::get($options, 'clientKey');

        $isProduction = carr::get($options, 'isProduction', true);
        $isSanitized = carr::get($options, 'isSanitized');
        $is3ds = carr::get($options, 'is3ds', true);
        $overrideNotifUrl = carr::get($options, 'overrideNotifUrl');
        CVendor_Midtrans_Config::$serverKey = $serverKey;
        CVendor_Midtrans_Config::$isProduction = $isProduction;
        if ($overrideNotifUrl !== null) {
            CVendor_Midtrans_Config::$overrideNotifUrl = $overrideNotifUrl;
        }
        if ($isSanitized !== null) {
            CVendor_Midtrans_Config::$isSanitized = $isSanitized;
        }
        if ($is3ds !== null) {
            CVendor_Midtrans_Config::$is3ds = $is3ds;
        }
        if ($clientKey !== null) {
            CVendor_Midtrans_Config::$clientKey = $clientKey;
        }
    }

    /**
     * @return CVendor_Midtrans_Snap
     */
    public function snap() {
        return new CBase_ForwarderStaticClass(CVendor_Midtrans_Snap::class);
    }

    public function notification() {
        return new CVendor_Midtrans_Notification();
    }
}
