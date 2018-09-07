<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 11:09:44 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Type that maps ab SQL BINARY/VARBINARY to a PHP resource stream.

 */
class CDatabase_Type_BinaryType extends CDatabase_Type {

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getBinaryTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        if (null === $value) {
            return null;
        }

        if (is_string($value)) {
            $fp = fopen('php://temp', 'rb+');
            fwrite($fp, $value);
            fseek($fp, 0);
            $value = $fp;
        }

        if (!is_resource($value)) {
            throw ConversionException::conversionFailed($value, self::BINARY);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return CDatabase_Type::BINARY;
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType() {
        return CDatabase_ParameterType::BINARY;
    }

}
