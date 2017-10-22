<?php

/**
 * @method InstagramAPI_Response_Model_DirectThread getThread()
 * @method InstagramAPI_Response_Model_User getUser()
 * @method bool isThread()
 * @method bool isUser()
 * @method setThread(InstagramAPI_Response_Model_DirectThread $value)
 * @method setUser(InstagramAPI_Response_Model_User $value)
 */
class InstagramAPI_Response_Model_DirectRankedRecipient extends InstagramAPI_AutoPropertyHandler {

    /**
     * @var InstagramAPI_Response_Model_DirectThread
     */
    public $thread;

    /**
     * @var InstagramAPI_Response_Model_User
     */
    public $user;

}
