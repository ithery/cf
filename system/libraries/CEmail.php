<?php

class CEmail {
    /**
     * @return \CEmail_Builder
     */
    public static function builder() {
        return CEmail_Builder::instance();
    }

    public static function sender(array $config = []) {
        return new CEmail_Sender($config);
    }

    public static function isValid($email) {
        $checker = new CEmail_Checker();

        return $checker->isValid($email);
    }
}
