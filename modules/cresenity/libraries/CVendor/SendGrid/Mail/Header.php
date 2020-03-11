<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used to construct a Header object for the /mail/send API call
 *
 * An object containing key/value pairs of header names and the value to substitute
 * for them. You must ensure these are properly encoded if they contain unicode
 * characters. Must not be one of the reserved headers
 *
 * @package SendGrid\Mail
 */
class CVendor_SendGrid_Mail_Header implements \JsonSerializable {

    /** @var $key string Header key */
    private $key;

    /** @var $value string Header value */
    private $value;

    /**
     * Optional constructor
     *
     * @param string|null $key Header key
     * @param string|null $value Header value
     */
    public function __construct($key = null, $value = null) {
        if (isset($key)) {
            $this->setKey($key);
        }
        if (isset($value)) {
            $this->setValue($value);
        }
    }

    /**
     * Add the key on a Header object
     *
     * @param string $key Header key
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setKey($key) {
        if (!is_string($key)) {
            throw new CVendor_SendGrid_Mail_TypeException('$key must be of type string.');
        }
        $this->key = $key;
    }

    /**
     * Retrieve the key from a Header object
     *
     * @return string
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * Add the value on a Header object
     *
     * @param string $value Header value
     * 
     * @throws CVendor_SendGrid_Mail_TypeException
     */
    public function setValue($value) {
        if (!is_string($value)) {
            throw new CVendor_SendGrid_Mail_TypeException('$value must be of type string.');
        }
        $this->value = $value;
    }

    /**
     * Retrieve the value from a Header object
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Return an array representing a Header object for the Twilio SendGrid API
     *
     * @return null|array
     */
    public function jsonSerialize() {
        return array_filter(
                        [
                    'key' => $this->getKey(),
                    'value' => $this->getValue()
                        ], function ($value) {
                    return $value !== null;
                }
                ) ?: null;
    }

}
