<?php

use PHPStan\Type\Type;
use PHPStan\Type\StringType;

class CQC_Phpstan_Service_Type_ModelProperty_ModelPropertyType extends StringType {
    /**
     * @param mixed[] $properties
     *
     * @return Type
     */
    public static function __set_state(array $properties): Type {
        return new self();
    }
}
