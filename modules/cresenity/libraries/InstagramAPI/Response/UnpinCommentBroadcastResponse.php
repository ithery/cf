<?php

/**
 * @method string getCommentId()
 * @method bool isCommentId()
 * @method setCommentId(string $value)
 */
class InstagramAPI_Response_UnpinCommentBroadcastResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var string
     */
    public $comment_id;

}
