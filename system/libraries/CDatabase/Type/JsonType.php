<?php

defined('SYSPATH') or die('No direct access allowed.');

class CDatabase_Type_JsonType extends CDatabase_Type {
    /**
     * @inheritdoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getJsonTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @inheritdoc
     */
    public function convertToDatabaseValue($value, CDatabase_Platform $platform) {
        if (null === $value) {
            return null;
        }

        $encoded = json_encode($value);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw CDatabase_Schema_Exception_ConversionException::conversionFailedSerialization($value, 'json', json_last_error_msg());
        }

        return $encoded;
    }

    /**
     * @inheritdoc
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        $val = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw CDatabase_Schema_Exception_ConversionException::conversionFailed($value, $this->getName());
        }

        return $val;
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return CDatabase_Type::JSON;
    }

    /**
     * @inheritdoc
     */
    public function requiresSQLCommentHint(CDatabase_Platform $platform) {
        return !$platform->hasNativeJsonType();
    }
}
