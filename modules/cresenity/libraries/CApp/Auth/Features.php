<?php

class CApp_Auth_Features {
    protected static $features;

    public static function setFeatures($features) {
        static::$features = $features;
    }

    /**
     * Enable the registration feature.
     *
     * @return string
     */
    public static function registration() {
        return 'registration';
    }

    /**
     * Enable the password reset feature.
     *
     * @return string
     */
    public static function resetPasswords() {
        return 'reset-passwords';
    }

    /**
     * Enable the email verification feature.
     *
     * @return string
     */
    public static function emailVerification() {
        return 'email-verification';
    }

    /**
     * Enable the update profile information feature.
     *
     * @return string
     */
    public static function updateProfileInformation() {
        return 'update-profile-information';
    }

    /**
     * Enable the update password feature.
     *
     * @return string
     */
    public static function updatePasswords() {
        return 'update-passwords';
    }

    /**
     * Enable the two factor authentication feature.
     *
     * @param array $options
     *
     * @return string
     */
    public static function twoFactorAuthentication(array $options = []) {
        return 'two-factor-authentication';
    }

    /**
     * Enable the teams feature.
     *
     * @return string
     */
    public static function teams() {
        return 'teams';
    }

    /**
     * Enable the profile photo upload feature.
     *
     * @return string
     */
    public static function profilePhotos() {
        return 'profile-photos';
    }

    /**
     * Determine if the given feature is enabled.
     *
     * @param string $feature
     *
     * @return bool
     */
    public static function enabled(string $feature) {
        return in_array($feature, static::$features);
    }
}
