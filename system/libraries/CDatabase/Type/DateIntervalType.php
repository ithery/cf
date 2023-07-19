<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Type that maps interval string to a PHP DateInterval Object.
 */
class CDatabase_Type_DateIntervalType extends CDatabase_Type {
    const FORMAT = '%RP%YY%MM%DDT%HH%IM%SS';

    /**
     * @inheritdoc
     */
    public function getName() {
        return CDatabase_Type::DATEINTERVAL;
    }

    /**
     * @inheritdoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        $fieldDeclaration['length'] = 255;

        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @inheritdoc
     */
    public function convertToDatabaseValue($value, CDatabase_Platform $platform) {
        if (null === $value) {
            return null;
        }

        if ($value instanceof \DateInterval) {
            return $value->format(self::FORMAT);
        }

        throw CDatabase_Schema_Exception_ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateInterval']);
    }

    /**
     * @inheritdoc
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        if ($value === null || $value instanceof \DateInterval) {
            return $value;
        }

        $negative = false;

        if (isset($value[0]) && ($value[0] === '+' || $value[0] === '-')) {
            $negative = $value[0] === '-';
            $value = substr($value, 1);
        }

        try {
            $interval = new \DateInterval($value);

            if ($negative) {
                $interval->invert = 1;
            }

            return $interval;
        } catch (\Exception $exception) {
            throw CDatabase_Schema_Exception_ConversionException::conversionFailedFormat($value, $this->getName(), self::FORMAT, $exception);
        }
    }

    /**
     * @inheritdoc
     */
    public function requiresSQLCommentHint(CDatabase_Platform $platform) {
        return true;
    }
}
