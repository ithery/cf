<?php

class CVendor_BunnyCDN {
    /**
     * @return CVendor_BunnyCDN_ApiFactory|CBase_ForwarderStaticClass
     */
    public static function api() {
        return new CBase_ForwarderStaticClass(CVendor_BunnyCDN_ApiFactory::class);
    }
}
