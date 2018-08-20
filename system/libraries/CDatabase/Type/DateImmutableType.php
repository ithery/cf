<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 11:09:44 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Immutable type of {@see DateType}.
 *
 */
class CDatabase_Type_DateImmutableType extends CDatabase_Type_DateType {

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return CDatabase_Type::DATE_IMMUTABLE;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, CDatabase_Platform $platform) {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value->format($platform->getDateFormatString());
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

        $dateTime = \DateTimeImmutable::createFromFormat('!' . $platform->getDateFormatString(), $value);

        if (!$dateTime) {
            throw ConversionException::conversionFailedFormat(
                    $value, $this->getName(), $platform->getDateFormatString()
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
