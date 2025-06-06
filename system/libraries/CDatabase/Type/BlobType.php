<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Type that maps an SQL BLOB to a PHP resource stream.
 */
class CDatabase_Type_BlobType extends CDatabase_Type {
    /**
     * @inheritdoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getBlobTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @inheritdoc
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
            throw CDatabase_Schema_Exception_ConversionException::conversionFailed($value, self::BLOB);
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return CDatabase_Type::BLOB;
    }

    /**
     * @inheritdoc
     */
    public function getBindingType() {
        return CDatabase_ParameterType::LARGE_OBJECT;
    }
}
