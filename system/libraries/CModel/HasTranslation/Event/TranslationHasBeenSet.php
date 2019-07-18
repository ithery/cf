<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 30, 2019, 3:50:03 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_HasTranslation_Event_TranslationHasBeenSet {

    /** @var CModel */
    public $model;

    /** @var string */
    public $key;

    /** @var string */
    public $locale;
    public $oldValue;
    public $newValue;

    public function __construct(CModel $model, $key, $locale, $oldValue, $newValue) {
        $this->model = $model;
        $this->key = $key;
        $this->locale = $locale;
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
    }

}
