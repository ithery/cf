<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 19, 2018, 3:39:01 AM
 */
class CApp_Model extends CModel {
    use CModel_SoftDelete_SoftDeleteTrait;

    public static function createModel($modelName) {
        $modelClass = 'CApp_Model_' . $modelName;
        return new $modelClass();
    }
}
