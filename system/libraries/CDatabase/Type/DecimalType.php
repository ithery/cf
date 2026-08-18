<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Type that maps an SQL DECIMAL to a PHP string.
 */
class CDatabase_Type_DecimalType extends CDatabase_Type {
    /**
     * @inheritdoc
     */
    public function getName() {
        return CDatabase_Type::DECIMAL;
    }

    /**
     * @inheritdoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getDecimalTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @inheritdoc
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        return $value;
    }
}
