<?php

defined('SYSPATH') or die('No direct access allowed.');

class CModel_HasTranslation_Event_TranslationHasBeenSet {
    /**
     * @var CModel
     */
    public $model;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $locale;

    /**
     * @var mixed
     */
    public $oldValue;

    /**
     * @var mixed
     */
    public $newValue;

    /**
     * Create a new event instance.
     *
     * @param CModel $model
     * @param string $key
     * @param string $locale
     * @param mixed  $oldValue
     * @param mixed  $newValue
     */
    public function __construct(CModel $model, $key, $locale, $oldValue, $newValue) {
        $this->model = $model;
        $this->key = $key;
        $this->locale = $locale;
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
    }
}
