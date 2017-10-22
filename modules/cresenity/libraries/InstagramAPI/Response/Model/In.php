<?php

/**
 * @method mixed getPosition()
 * @method mixed getTimeInVideo()
 * @method mixed getUser()
 * @method bool isPosition()
 * @method bool isTimeInVideo()
 * @method bool isUser()
 * @method setPosition(mixed $value)
 * @method setTimeInVideo(mixed $value)
 * @method setUser(mixed $value)
 */
class InstagramAPI_Response_Model_In extends InstagramAPI_AutoPropertyHandler {
    /*
     * @var InstagramAPI_Response_Model_Position
     */

    public $position;
    /*
     * @var InstagramAPI_Response_Model_User
     */
    public $user;
    public $time_in_video;

}
