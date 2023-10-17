<?php
/**
 * @see https://github.com/Spomky-Labs/otphp/
 */
class CAuth_OTP {
    public static function hotp($secret = null) {
        if ($secret == null) {
            return CAuth_OTP_HOTP::generate();
        }

        return CAuth_OTP_HOTP::createFromSecret($secret);
    }

    public static function totp($secret = null) {
        if ($secret == null) {
            return CAuth_OTP_TOTP::generate();
        }

        return CAuth_OTP_TOTP::createFromSecret($secret);
    }
}
