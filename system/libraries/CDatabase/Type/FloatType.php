<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 11:09:44 AM
 */
class CDatabase_Type_FloatType extends CDatabase_Type {
    /**
     * {@inheritdoc}
     */
    public function getName() {
        return CDatabase_Type::FLOAT;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getFloatDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        return (null === $value) ? null : (double) $value;
    }
}
