<?php

final class CVendor_Firebase_Auth_CreateActionLink {
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $email;

    /**
     * @var CVendor_Firebase_Auth_ActionCodeSettingsInterface
     */
    private $settings;

    /**
     * @var null|string
     */
    private $tenantId = null;

    /**
     * @var null|string
     */
    private $locale = null;

    /**
     * @param string                                            $type
     * @param string                                            $email
     * @param CVendor_Firebase_Auth_ActionCodeSettingsInterface $settings
     */
    private function __construct($type, $email, CVendor_Firebase_Auth_ActionCodeSettingsInterface $settings) {
        $this->type = $type;
        $this->email = $email;
        $this->settings = $settings;
    }

    /**
     * @param string                                            $type
     * @param \Stringable|string                                $email
     * @param CVendor_Firebase_Auth_ActionCodeSettingsInterface $settings
     * @param null|string                                       $tenantId
     * @param null|string                                       $locale
     *
     * @return self
     */
    public static function new($type, $email, CVendor_Firebase_Auth_ActionCodeSettingsInterface $settings, $tenantId = null, $locale = null) {
        $email = (string) (new CVendor_Firebase_Value_Email((string) $email));

        $instance = new self($type, $email, $settings);
        $instance->tenantId = $tenantId;
        $instance->locale = $locale;

        return $instance;
    }

    /**
     * @return string
     */
    public function type() {
        return $this->type;
    }

    /**
     * @return string
     */
    public function email() {
        return $this->email;
    }

    /**
     * @return CVendor_Firebase_Auth_ActionCodeSettingsInterface
     */
    public function settings() {
        return $this->settings ?: CVendor_Firebase_Auth_ActionCodeSettings_ValidatedActionCodeSettings::empty();
    }

    /**
     * @return null|string
     */
    public function tenantId() {
        return $this->tenantId;
    }

    /**
     * @return null|string
     */
    public function locale() {
        return $this->locale;
    }
}
