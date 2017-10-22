<?php

/**
 * @method mixed getBroadcastMessage()
 * @method InstagramAPI_Response_Model_User getBroadcastOwner()
 * @method mixed getBroadcastStatus()
 * @method string getId()
 * @method string getMediaId()
 * @method mixed getOrganicTrackingToken()
 * @method mixed getPublishedTime()
 * @method bool isBroadcastMessage()
 * @method bool isBroadcastOwner()
 * @method bool isBroadcastStatus()
 * @method bool isId()
 * @method bool isMediaId()
 * @method bool isOrganicTrackingToken()
 * @method bool isPublishedTime()
 * @method setBroadcastMessage(mixed $value)
 * @method setBroadcastOwner(InstagramAPI_Response_Model_User $value)
 * @method setBroadcastStatus(mixed $value)
 * @method setId(string $value)
 * @method setMediaId(string $value)
 * @method setOrganicTrackingToken(mixed $value)
 * @method setPublishedTime(mixed $value)
 */
class InstagramAPI_Response_BroadcastInfoResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var string
     */
    public $id;
    public $broadcast_message;
    public $organic_tracking_token;
    public $published_time;
    public $broadcast_status;

    /**
     * @var string
     */
    public $media_id;

    /**
     * @var InstagramAPI_Response_Model_User
     */
    public $broadcast_owner;

}
