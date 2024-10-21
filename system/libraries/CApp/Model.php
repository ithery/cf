<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property null|string    $createdby
 * @property null|string    $updatedby
 * @property CCarbon|string $created
 * @property CCarbon|string $updated
 * @property int            $status
 */
class CApp_Model extends CModel {
    use CModel_SoftDelete_SoftDeleteTrait;

    /**
     * @param string $modelName
     *
     * @deprecated 1.6
     *
     * @return CApp_Model
     */
    public static function createModel($modelName) {
        $modelClass = 'CApp_Model_' . $modelName;

        return new $modelClass();
    }
}
