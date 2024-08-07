<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Immutable type of {@see DateTimeType}.
 */
class CDatabase_Type_DateTimeImmutableType extends CDatabase_Type_DateTimeType {
    /**
     * @inheritdoc
     */
    public function getName() {
        return CDatabase_Type::DATETIME_IMMUTABLE;
    }

    /**
     * @inheritdoc
     */
    public function convertToDatabaseValue($value, CDatabase_Platform $platform) {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value->format($platform->getDateTimeFormatString());
        }

        throw CDatabase_Schema_Exception_ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', \DateTimeImmutable::class]
        );
    }

    /**
     * @inheritdoc
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        if ($value === null || $value instanceof \DateTimeImmutable) {
            return $value;
        }

        $dateTime = \DateTimeImmutable::createFromFormat($platform->getDateTimeFormatString(), $value);

        if (!$dateTime) {
            $dateTime = \date_create_immutable($value);
        }

        if (!$dateTime) {
            throw CDatabase_Schema_Exception_ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        return $dateTime;
    }

    /**
     * @inheritdoc
     */
    public function requiresSQLCommentHint(CDatabase_Platform $platform) {
        return true;
    }
}
