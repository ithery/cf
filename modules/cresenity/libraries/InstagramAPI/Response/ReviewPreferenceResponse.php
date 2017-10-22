<?php

/**
 * @method InstagramAPI_Response_Model_User getUser()
 * @method bool isUser()
 * @method setUser(InstagramAPI_Response_Model_User $value)
 */
class InstagramAPI_Response_ReviewPreferenceResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_User
     */
    public $user;

}
