<?php

/**
 * @method mixed getLikes()
 * @method bool isLikes()
 * @method setLikes(mixed $value)
 */
class InstagramAPI_Response_BroadcastLikeResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $likes;

}
