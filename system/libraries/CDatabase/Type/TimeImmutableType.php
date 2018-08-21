<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 11:09:44 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Immutable type of {@see TimeType}.
 *
 */
class CDatabase_Type_TimeImmutableType extends CDatabase_Type_TimeType {

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return CDatabase_Type::TIME_IMMUTABLE;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, CDatabase_Platform $platform) {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value->format($platform->getTimeFormatString());
        }

        throw ConversionException::conversionFailedInvalidType(
                $value, $this->getName(), ['null', \DateTimeImmutable::class]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        if ($value === null || $value instanceof \DateTimeImmutable) {
            return $value;
        }

        $dateTime = \DateTimeImmutable::createFromFormat('!' . $platform->getTimeFormatString(), $value);

        if (!$dateTime) {
            throw ConversionException::conversionFailedFormat(
                    $value, $this->getName(), $platform->getTimeFormatString()
            );
        }

        return $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(CDatabase_Platform $platform) {
        return true;
    }

}
