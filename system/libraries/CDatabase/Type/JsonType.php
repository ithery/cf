<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 11:06:44 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDatabase_Type_JsonType extends CDatabase_Type {

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getJsonTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, CDatabase_Platform $platform) {
        if (null === $value) {
            return null;
        }

        $encoded = json_encode($value);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw ConversionException::conversionFailedSerialization($value, 'json', json_last_error_msg());
        }

        return $encoded;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        $val = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $val;
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return CDatabase_Type::JSON;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(CDatabase_Platform $platform) {
        return !$platform->hasNativeJsonType();
    }

}
