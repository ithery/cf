<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Type that maps an SQL DATE to a PHP Date object.
 */
class CDatabase_Type_DateType extends CDatabase_Type {
    /**
     * @inheritdoc
     */
    public function getName() {
        return CDatabase_Type::DATE;
    }

    /**
     * @inheritdoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getDateTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @inheritdoc
     */
    public function convertToDatabaseValue($value, CDatabase_Platform $platform) {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format($platform->getDateFormatString());
        }

        throw CDatabase_Schema_Exception_ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateTime']);
    }

    /**
     * @inheritdoc
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        if ($value === null || $value instanceof \DateTimeInterface) {
            return $value;
        }

        $val = \DateTime::createFromFormat('!' . $platform->getDateFormatString(), $value);
        if (!$val) {
            throw CDatabase_Schema_Exception_ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateFormatString());
        }

        return $val;
    }
}
