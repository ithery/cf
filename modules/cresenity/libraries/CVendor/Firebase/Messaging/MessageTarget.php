<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class CVendor_Firebase_Messaging_MessageTarget {

    const CONDITION = 'condition';
    const TOKEN = 'token';
    const TOPIC = 'topic';
    const TYPES = [
        self::CONDITION, self::TOKEN, self::TOPIC,
    ];

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $value;

    private function __construct() {
        
    }

    /**
     * Create a new message target with the given type and value.
     *
     * @throws InvalidArgumentException
     *
     * @return MessageTarget
     */
    public static function with($type, $value) {
        $targetType = \mb_strtolower($type);

        $new = new self();
        $new->type = $targetType;

        switch ($targetType) {
            case self::CONDITION:
                $new->value = (string) CVendor_Firebase_Messaging_Condition::fromValue($value);
                break;
            case self::TOKEN:
                $new->value = (string) CVendor_Firebase_Messaging_RegistrationToken::fromValue($value);
                break;
            case self::TOPIC:
                $new->value = (string) CVendor_Firebase_Messaging_Topic::fromValue($value);
                break;
            default:
                throw new CVendor_Firebase_Exception_InvalidArgumentException("Invalid target type '{$type}', valid types: " . \implode(', ', self::TYPES));
        }

        return $new;
    }

    public function type() {
        return $this->type;
    }

    public function value() {
        return $this->value;
    }

}
