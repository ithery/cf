<?php

/**
 * Description of Content
 *
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jan 7, 2018, 12:35:25 PM
 */
class CSendGrid_Content implements \JsonSerializable {
    private $type;

    private $value;

    public function __construct($type, $value) {
        $this->type = $type;
        $this->value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function setValue($value) {
        $this->value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }

    public function getValue() {
        return $this->value;
    }

    public function jsonSerialize() {
        return array_filter(
            [
                'type' => $this->getType(),
                'value' => $this->getValue()
            ],
            function ($value) {
                return $value !== null;
            }
        ) ?: null;
    }
}
