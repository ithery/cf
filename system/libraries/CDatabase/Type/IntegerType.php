<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 11:10:50 AM
 */

/**
 * Type that maps an SQL INT to a PHP integer.
 */
class CDatabase_Type_IntegerType extends CDatabase_Type implements CDatabase_Type_Interface_PhpIntegerMappingTypeInterface {
    /**
     * {@inheritdoc}
     */
    public function getName() {
        return CDatabase_Type::INTEGER;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getIntegerTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        return (null === $value) ? null : (int) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType() {
        return CDatabase_ParameterType::INTEGER;
    }
}
