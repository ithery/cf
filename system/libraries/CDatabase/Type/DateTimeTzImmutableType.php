<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Immutable type of {@see DateTimeTzType}.
 */
class CDatabase_Type_DateTimeTzImmutableType extends CDatabase_Type_DateTimeTzType {
    /**
     * @inheritdoc
     */
    public function getName() {
        return CDatabase_Type::DATETIMETZ_IMMUTABLE;
    }

    /**
     * @inheritdoc
     */
    public function convertToDatabaseValue($value, CDatabase_Platform $platform) {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value->format($platform->getDateTimeTzFormatString());
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

        $dateTime = \DateTimeImmutable::createFromFormat($platform->getDateTimeTzFormatString(), $value);

        if (!$dateTime) {
            throw CDatabase_Schema_Exception_ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeTzFormatString()
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
