<?php

/**
 * @method mixed getClientTime()
 * @method mixed getPosition()
 * @method InstagramAPI_Response_Model_User getUser()
 * @method bool isClientTime()
 * @method bool isPosition()
 * @method bool isUser()
 * @method setClientTime(mixed $value)
 * @method setPosition(mixed $value)
 * @method setUser(User $value)
 */
class InstagramAPI_Response_Model_Suggested extends InstagramAPI_AutoPropertyHandler {

    public $position;

    /**
     * @var InstagramAPI_Response_Model_User
     */
    public $user;
    public $client_time;

}
