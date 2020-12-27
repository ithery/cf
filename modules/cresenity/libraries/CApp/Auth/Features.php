<?php

class CApp_Auth_Features {
    protected $features;

    public function setFeatures($features) {
        $this->features = $features;
        return $this;
    }

    /**
     * Enable the registration feature.
     *
     * @return string
     */
    public function registration() {
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
}
