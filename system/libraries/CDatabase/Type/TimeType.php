<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 11:09:44 AM
 */

/**
 * Type that maps an SQL TIME to a PHP DateTime object.
 *
 * @since 2.0
 */
class CDatabase_Type_TimeType extends CDatabase_Type {
    /**
     * {@inheritdoc}
     */
    public function getName() {
        return CDatabase_Type::TIME;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getTimeTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, CDatabase_Platform $platform) {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format($platform->getTimeFormatString());
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateTime']);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        if ($value === null || $value instanceof \DateTimeInterface) {
            return $value;
        }

        $val = \DateTime::createFromFormat('!' . $platform->getTimeFormatString(), $value);
        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getTimeFormatString());
        }

        return $val;
    }
}
