<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 19, 2018, 3:39:01 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Model extends CModel {

    use CModel_SoftDelete_Trait;

    public static function createModel($modelName) {
        $modelClass = 'CApp_Model_' . $modelName;
        return new $modelClass();
    }

}
