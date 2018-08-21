<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 11:05:43 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDatabase_Type_JsonArrayType extends CDatabase_Type_JsonType {

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        if ($value === null || $value === '') {
            return [];
        }

        $value = (is_resource($value)) ? stream_get_contents($value) : $value;

        return json_decode($value, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return CDatabase_Type::JSON_ARRAY;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(CDatabase_Platform $platform) {
        return true;
    }

}
