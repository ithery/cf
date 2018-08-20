<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 11:09:44 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * DateTime type saving additional timezone information.
 *
 * Caution: Databases are not necessarily experts at storing timezone related
 * data of dates. First, of all the supported vendors only PostgreSQL and Oracle
 * support storing Timezone data. But those two don't save the actual timezone
 * attached to a DateTime instance (for example "Europe/Berlin" or "America/Montreal")
 * but the current offset of them related to UTC. That means depending on daylight saving times
 * or not you may get different offsets.
 *
 * This datatype makes only sense to use, if your application works with an offset, not
 * with an actual timezone that uses transitions. Otherwise your DateTime instance
 * attached with a timezone such as Europe/Berlin gets saved into the database with
 * the offset and re-created from persistence with only the offset, not the original timezone
 * attached.
 *
 */
class CDatabase_Type_DateTimeTzType extends CDatabase_Type implements CDatabase_Type_Interface_PhpDateTimeMappingTypeInterface {

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return CDatabase_Type::DATETIMETZ;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getDateTimeTzTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, CDatabase_Platform $platform) {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format($platform->getDateTimeTzFormatString());
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

        $val = \DateTime::createFromFormat($platform->getDateTimeTzFormatString(), $value);
        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeTzFormatString());
        }

        return $val;
    }

}
