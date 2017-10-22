<?php

/**
 * @method mixed getButtonText()
 * @method mixed getCameraTarget()
 * @method mixed getCardId()
 * @method mixed getImageUrl()
 * @method mixed getMessage()
 * @method mixed getTitle()
 * @method bool isButtonText()
 * @method bool isCameraTarget()
 * @method bool isCardId()
 * @method bool isImageUrl()
 * @method bool isMessage()
 * @method bool isTitle()
 * @method setButtonText(mixed $value)
 * @method setCameraTarget(mixed $value)
 * @method setCardId(mixed $value)
 * @method setImageUrl(mixed $value)
 * @method setMessage(mixed $value)
 * @method setTitle(mixed $value)
 */
class InstagramAPI_Response_Model_DismissCard extends InstagramAPI_AutoPropertyHandler {

    public $card_id;
    public $image_url;
    public $title;
    public $message;
    public $button_text;
    public $camera_target;

}
