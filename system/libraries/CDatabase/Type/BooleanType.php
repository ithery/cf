<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 11:09:44 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Type that maps an SQL boolean to a PHP boolean.
 *
 * @since 2.0
 */
class CDatabase_Type_BooleanType extends CDatabase_Type {

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getBooleanTypeDeclarationSQL($fieldDeclaration);
    }
    

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, CDatabase_Platform $platform) {
        return $platform->convertBooleansToDatabaseValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        return $platform->convertFromBoolean($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return CDatabase_Type::BOOLEAN;
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType() {
        return CDatabase_ParameterType::BOOLEAN;
    }

}
