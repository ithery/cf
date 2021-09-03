<?php

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
     * @param mixed $type
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     *
     * @return CVendor_Firebase_Messaging_MessageTarget
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
