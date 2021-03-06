<?php

/**
 * This class is used to construct a OpenTracking object for the /mail/send API call
 *
 * Allows you to track whether the email was opened or not, but including a single
 * pixel image in the body of the content. When the pixel is loaded, we can log that
 * the email was opened
 *
 * @package SendGrid\Mail
 */
class CVendor_SendGrid_Mail_OpenTracking implements \JsonSerializable {
    /**
     * @var bool Indicates if this setting is enabled
     */
    private $enable;

    /**
     * @var string Allows you to specify a substitution tag that you can insert in the body of your email at a location that you desire. This tag will be replaced by the open tracking pixel
     */
    private $substitution_tag;

    /**
     * Optional constructor
     *
     * @param bool|null   $enable           Indicates if this setting is enabled
     * @param string|null $substitution_tag Allows you to specify a substitution
     *                                      tag that you can insert in the body
     *                                      of your email at a location that you
     *                                      desire. This tag will be replaced by
     *                                      the open tracking pixel
     */
    public function __construct($enable = null, $substitution_tag = null) {
        if (isset($enable)) {
            $this->setEnable($enable);
        }
        if (isset($substitution_tag)) {
            $this->setSubstitutionTag($substitution_tag);
        }
    }

    /**
     * Update the enable setting on a OpenTracking object
     *
     * @param bool $enable Indicates if this setting is enabled
     *
     * @throws CVendor_SendGrid_Exception_TypeException
     */
    public function setEnable($enable) {
        if (!is_bool($enable)) {
            throw new CVendor_SendGrid_Exception_TypeException('$enable must be of type bool');
        }
        $this->enable = $enable;
    }

    /**
     * Retrieve the enable setting on a OpenTracking object
     *
     * @return bool
     */
    public function getEnable() {
        return $this->enable;
    }

    /**
     * Set the substitution tag on a OpenTracking object
     *
     * @param string $substitution_tag Allows you to specify a substitution
     *                                 tag that you can insert in the body
     *                                 of your email at a location that you
     *                                 desire. This tag will be replaced by
     *                                 the open tracking pixel
     *
     * @throws CVendor_SendGrid_Exception_TypeException
     */
    public function setSubstitutionTag($substitution_tag) {
        if (!is_string($substitution_tag)) {
            throw new CVendor_SendGrid_Exception_TypeException('$substitution_tag must be of type string.');
        }
        $this->substitution_tag = $substitution_tag;
    }

    /**
     * Retrieve the substitution tag from a OpenTracking object
     *
     * @return string
     */
    public function getSubstitutionTag() {
        return $this->substitution_tag;
    }

    /**
     * Return an array representing a OpenTracking object for the Twilio SendGrid API
     *
     * @return null|array
     */
    public function jsonSerialize() {
        return array_filter(
            [
                'enable' => $this->getEnable(),
                'substitution_tag' => $this->getSubstitutionTag()
            ],
            function ($value) {
                return $value !== null;
            }
        ) ?: null;
    }
}
