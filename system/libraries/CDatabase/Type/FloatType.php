<?php

defined('SYSPATH') or die('No direct access allowed.');

class CDatabase_Type_FloatType extends CDatabase_Type {
    /**
     * @inheritdoc
     */
    public function getName() {
        return CDatabase_Type::FLOAT;
    }

    /**
     * @inheritdoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getFloatDeclarationSQL($fieldDeclaration);
    }

    /**
     * @inheritdoc
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        return (null === $value) ? null : (double) $value;
    }
}
