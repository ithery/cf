<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Type that maps an SQL VARCHAR to a PHP string.
 *
 * @since 2.0
 */
class CDatabase_Type_StringType extends CDatabase_Type {
    /**
     * @inheritdoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @inheritdoc
     */
    public function getDefaultLength(CDatabase_Platform $platform) {
        return $platform->getVarcharDefaultLength();
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return CDatabase_Type::STRING;
    }
}
