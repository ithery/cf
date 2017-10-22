<?php

/**
 * @method In[] getIn()
 * @method mixed getPhotoOfYou()
 * @method bool isIn()
 * @method bool isPhotoOfYou()
 * @method setIn(In[] $value)
 * @method setPhotoOfYou(mixed $value)
 */
class InstagramAPI_Response_Model_Usertag extends InstagramAPI_AutoPropertyHandler {

    /**
     * @var InstagramAPI_Response_Model_In[]
     */
    public $in;
    public $photo_of_you;

}
