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

    /**
     * @param string $name
     *
     * @return CEmail_Contract_MailerInterface
     */
    public static function mailer($name = '') {
        return CEmail_MailManager::instance()->mailer($name);
    }
}
