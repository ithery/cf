<?php

/**
 * @method mixed getMegaphone()
 * @method InstagramAPI_Response_Model_User getUser()
 * @method bool isMegaphone()
 * @method bool isUser()
 * @method setMegaphone(mixed $value)
 * @method setUser(InstagramAPI_Response_Model_User $value)
 */
class InstagramAPI_Response_UserInfoResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $megaphone;

    /**
     * @var InstagramAPI_Response_Model_User
     */
    public $user;

}
