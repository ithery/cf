<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 11:19:16 AM
 */
class CDatabase_Type_BigIntType extends CDatabase_Type implements CDatabase_Type_Interface_PhpIntegerMappingTypeInterface {
    /**
     * {@inheritdoc}
     */
    public function getName() {
        return CDatabase_Type::BIGINT;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getBigIntTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType() {
        return CDatabase_ParameterType::STRING;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        return (null === $value) ? null : (string) $value;
    }
}
