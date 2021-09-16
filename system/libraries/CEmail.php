<?php

class CEmail {
    /**
     * @return \CEmail_Builder
     */
    public static function builder() {
        return CEmail_Builder::instance();
    }

    public static function sender(array $config = []) {
        if (c::blank($config)) {
            //config
            $username = CF::config('app.email.username', CF::config('app.smtp_username'));
        }
        return new CEmail_Sender($config);
    }
}
