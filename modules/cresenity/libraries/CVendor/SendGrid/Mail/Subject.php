<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used to construct a Subject object for the /mail/send API call
 *
 * @package SendGrid\Mail
 */
class CVendor_SendGrid_Mail_Subject implements \JsonSerializable {

    /** @var $subject string The email subject */
    private $subject;

    /**
     * Optional constructor
     *
     * @param string|null $subject The email subject
     */
    public function __construct($subject = null) {
        if (isset($subject)) {
            $this->setSubject($subject);
        }
    }

    /**
     * Set the subject on a Subject object
     *
     * @param string $subject The email subject
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setSubject($subject) {
        if (!is_string($subject)) {
            throw new CVendor_SendGrid_Mail_TypeException('$subject must be of type string.');
        }

        $this->subject = $subject;
    }

    /**
     * Retrieve the subject from a Subject object
     *
     * @return string
     */
    public function getSubject() {
        return mb_convert_encoding($this->subject, 'UTF-8', 'UTF-8');
    }

    /**
     * Return an array representing a Subject object for the Twilio SendGrid API
     *
     * @return string
     */
    public function jsonSerialize() {
        return $this->getSubject();
    }

}
