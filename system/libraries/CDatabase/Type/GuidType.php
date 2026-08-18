<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Represents a GUID/UUID datatype (both are actually synonyms) in the database.
 */
class CDatabase_Type_GuidType extends CDatabase_Type_StringType {
    /**
     * @inheritdoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getGuidTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return CDatabase_Type::GUID;
    }

    /**
     * @inheritdoc
     */
    public function requiresSQLCommentHint(CDatabase_Platform $platform) {
        return !$platform->hasNativeGuidType();
    }
}
