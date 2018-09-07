<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 11:19:16 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */


/**
 * Type that maps a database BIGINT to a PHP string.
 *
 * @author robo
 * @since 2.0
 */
class CDatabase_Type_BigIntType extends CDatabase_Type implements CDatabase_Type_Interface_PhpIntegerMappingTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return CDatabase_Type::BIGINT;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform)
    {
        return $platform->getBigIntTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType()
    {
        return CDatabase_ParameterType::STRING;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform)
    {
        return (null === $value) ? null : (string) $value;
    }
}
