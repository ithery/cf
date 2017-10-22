<?php

/**
 * @method mixed getButtons()
 * @method mixed getDescription()
 * @method mixed getGatingType()
 * @method mixed getTitle()
 * @method bool isButtons()
 * @method bool isDescription()
 * @method bool isGatingType()
 * @method bool isTitle()
 * @method setButtons(mixed $value)
 * @method setDescription(mixed $value)
 * @method setGatingType(mixed $value)
 * @method setTitle(mixed $value)
 */
class InstagramAPI_Response_Model_Gating extends InstagramAPI_AutoPropertyHandler {

    public $gating_type;
    public $description;
    public $buttons;
    public $title;

}
