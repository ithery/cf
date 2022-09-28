<?php

class CWebhook {
    /**
     * @return CWebhook_Server|CBase_ForwarderStaticClass
     */
    public static function server() {
        return new CBase_ForwarderStaticClass(CWebhook_Server::class);
    }

    /**
     * @return CWebhook_Client|CBase_ForwarderStaticClass
     */
    public static function client() {
        return new CBase_ForwarderStaticClass(CWebhook_Client::class);
    }
}
