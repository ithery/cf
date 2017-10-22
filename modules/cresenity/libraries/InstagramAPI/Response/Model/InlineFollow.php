<?php

/**
 * @method mixed getFollowing()
 * @method mixed getOutgoingRequest()
 * @method InstagramAPI_Response_Model_User getUserInfo()
 * @method bool isFollowing()
 * @method bool isOutgoingRequest()
 * @method bool isUserInfo()
 * @method setFollowing(mixed $value)
 * @method setOutgoingRequest(mixed $value)
 * @method setUserInfo(InstagramAPI_Response_Model_User $value)
 */
class InstagramAPI_Response_Model_InlineFollow extends InstagramAPI_AutoPropertyHandler {

    /**
     * @var InstagramAPI_Response_Model_User
     */
    public $user_info;
    public $following;
    public $outgoing_request;

}
