<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used to construct a BypassListManagement object for
 * the /mail/send API call
 *
 * Allows you to bypass all unsubscribe groups and suppressions to
 * ensure that the email is delivered to every single recipient. This
 * should only be used in emergencies when it is absolutely necessary
 * that every recipient receives your email
 *
 * @package SendGrid\Mail
 */
class CVendor_SendGrid_Mail_BypassListManagement implements \JsonSerializable {

    /** @var $enable bool Indicates if this setting is enabled */
    private $enable;

    /**
     * Optional constructor
     *
     * @param bool|null $enable Indicates if this setting is enabled
     */
    public function __construct($enable = null) {
        if (isset($enable)) {
            $this->setEnable($enable);
        }
    }

    /**
     * Update the enable setting on a BypassListManagement object
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
     * Retrieve the enable setting on a BypassListManagement object
     *
     * @return bool
     */
    public function getEnable() {
        return $this->enable;
    }

    /**
     * Return an array representing a BypassListManagement object for
     * the SendGrid API
     *
     * @return null|array
     */
    public function jsonSerialize() {
        return array_filter(
                        [
                    'enable' => $this->getEnable()
                        ], function ($value) {
                    return $value !== null;
                }
                ) ?: null;
    }

}
