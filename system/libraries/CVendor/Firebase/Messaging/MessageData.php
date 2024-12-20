<?php

final class CVendor_Firebase_Messaging_MessageData implements \JsonSerializable {
    /**
     * @var array
     */
    private $data = [];

    private function __construct() {
    }

    public static function fromArray(array $data) {
        $messageData = new self();

        foreach ($data as $key => $value) {
            if (!self::isStringable($key) || !self::isStringable($value)) {
                $additionalMessage = '';
                if (!self::isStringable($key)) {
                    $additionalMessage = ' (You have key not string: ' . json_encode($key) . ')';
                }
                if (!self::isStringable($value)) {
                    $additionalMessage = ' (You have not stringable value on key: ' . json_encode($key) . ')';
                }

                throw new CVendor_Firebase_Exception_InvalidArgumentException('Message data must be a one-dimensional array of string(able) keys and values.' . $additionalMessage . '');
            }

            $messageData->data[(string) $key] = (string) $value;
        }

        return $messageData;
    }

    public function jsonSerialize() {
        return $this->data;
    }

    private static function isStringable($value) {
        return \is_scalar($value) || (\is_object($value) && \method_exists($value, '__toString'));
    }
}
