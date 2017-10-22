<?php

/**
 * @method mixed getLikeTs()
 * @method InstagramAPI_Response_Model_User[] getLikers()
 * @method mixed getLikes()
 * @method bool isLikeTs()
 * @method bool isLikers()
 * @method bool isLikes()
 * @method setLikeTs(mixed $value)
 * @method setLikers(InstagramAPI_Response_Model_User[] $value)
 * @method setLikes(mixed $value)
 */
class InstagramAPI_Response_BroadcastLikeCountResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $like_ts;
    public $likes;

    /**
     * @var InstagramAPI_Response_Model_User[]
     */
    public $likers;

}
