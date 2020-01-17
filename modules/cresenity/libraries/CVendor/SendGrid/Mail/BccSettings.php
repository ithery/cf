<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used to construct a BccSettings object for the /mail/send API call
 *
 * @package SendGrid\Mail
 */
class CVendor_SendGrid_Mail_BccSettings implements \JsonSerializable {

    /** @var $enable bool Indicates if this setting is enabled */
    private $enable;

    /** @var $email string The email address that you would like to receive the BCC */
    private $email;

    /**
     * Optional constructor
     *
     * @param bool|null $enable Indicates if this setting is enabled
     * @param string|null $email The email address that you would like
     *                            to receive the BCC
     */
    public function __construct($enable = null, $email = null) {
        if (isset($enable)) {
            $this->setEnable($enable);
        }
        if (isset($email)) {
            $this->setEmail($email);
        }
    }

    /**
     * Update the enable setting on a CVendor_SendGrid_Mail_BccSettings object
     *
     * @param bool $enable Indicates if this setting is enabled
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setEnable($enable) {
        if (!is_bool($enable)) {
            throw new CVendor_SendGrid_Mail_TypeException('$enable must be of type bool.');
        }
        $this->enable = $enable;
    }

    /**
     * Retrieve the enable setting on a BccSettings object
     *
     * @return bool
     */
    public function getEnable() {
        return $this->enable;
    }

    /**
     * Add the email setting on a CVendor_SendGrid_Mail_BccSettings object
     *
     * @param string $email The email address that you would like
     *                      to receive the BCC
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setEmail($email) {
        if (!is_string($email) &&
                filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            throw new CVendor_SendGrid_Mail_TypeException(
            '$email must valid and be of type string.'
            );
        }
        $this->email = $email;
    }

    /**
     * Retrieve the email setting on a CVendor_SendGrid_Mail_BccSettings object
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Return an array representing a CVendor_SendGrid_Mail_BccSettings object for the Twilio SendGrid API
     *
     * @return null|array
     */
    public function jsonSerialize() {
        return array_filter(
                        [
                    'enable' => $this->getEnable(),
                    'email' => $this->getEmail()
                        ], function ($value) {
                    return $value !== null;
                }
                ) ?: null;
    }

}
