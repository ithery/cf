<?php

/**
 * @method mixed getObfuscatedPhoneNumber()
 * @method InstagramAPI_Response_Model_PhoneVerificationSettings getPhoneVerificationSettings()
 * @method mixed getTwoFactorIdentifier()
 * @method mixed getUsername()
 * @method bool isObfuscatedPhoneNumber()
 * @method bool isPhoneVerificationSettings()
 * @method bool isTwoFactorIdentifier()
 * @method bool isUsername()
 * @method setObfuscatedPhoneNumber(mixed $value)
 * @method setPhoneVerificationSettings(PhoneVerificationSettings $value)
 * @method setTwoFactorIdentifier(mixed $value)
 * @method setUsername(mixed $value)
 */
class InstagramAPI_Response_Model_TwoFactorInfo extends InstagramAPI_AutoPropertyHandler {

    public $username;
    public $two_factor_identifier;

    /**
     * @var InstagramAPI_Response_Model_PhoneVerificationSettings
     */
    public $phone_verification_settings;
    public $obfuscated_phone_number;

}
