<?php

/**
 * @method mixed getObfuscatedPhoneNumber()
 * @method InstagramAPI_Response_Model_PhoneVerificationSettings getPhoneVerificationSettings()
 * @method bool isObfuscatedPhoneNumber()
 * @method bool isPhoneVerificationSettings()
 * @method setObfuscatedPhoneNumber(mixed $value)
 * @method setPhoneVerificationSettings(InstagramAPI_Response_Model_PhoneVerificationSettings $value)
 */
class InstagramAPI_Response_RequestTwoFactorResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_PhoneVerificationSettings
     */
    public $phone_verification_settings;
    public $obfuscated_phone_number;

}
