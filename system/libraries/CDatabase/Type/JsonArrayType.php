<?php

defined('SYSPATH') or die('No direct access allowed.');

class CDatabase_Type_JsonArrayType extends CDatabase_Type_JsonType {
    /**
     * @inheritdoc
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        if ($value === null || $value === '') {
            return [];
        }

        $value = (is_resource($value)) ? stream_get_contents($value) : $value;

        return json_decode($value, true);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return CDatabase_Type::JSON_ARRAY;
    }

    /**
     * @inheritdoc
     */
    public function requiresSQLCommentHint(CDatabase_Platform $platform) {
        return true;
    }
}
