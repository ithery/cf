<?php

/**
 * @method InstagramAPI_Response_Model_Broadcast getBroadcast()
 * @method mixed getCanReply()
 * @method mixed getExpiringAt()
 * @method string getId()
 * @method InstagramAPI_Response_Model_Item[] getItems()
 * @method mixed getLatestReelMedia()
 * @method InstagramAPI_Response_Model_Location getLocation()
 * @method mixed getPrefetchCount()
 * @method mixed getSeen()
 * @method InstagramAPI_Response_Model_User getUser()
 * @method bool isBroadcast()
 * @method bool isCanReply()
 * @method bool isExpiringAt()
 * @method bool isId()
 * @method bool isItems()
 * @method bool isLatestReelMedia()
 * @method bool isLocation()
 * @method bool isPrefetchCount()
 * @method bool isSeen()
 * @method bool isUser()
 * @method setBroadcast(InstagramAPI_Response_Model_Broadcast $value)
 * @method setCanReply(mixed $value)
 * @method setExpiringAt(mixed $value)
 * @method setId(string $value)
 * @method setItems(InstagramAPI_Response_Model_Item[] $value)
 * @method setLatestReelMedia(mixed $value)
 * @method setLocation(InstagramAPI_Response_Model_Location $value)
 * @method setPrefetchCount(mixed $value)
 * @method setSeen(mixed $value)
 * @method setUser(InstagramAPI_Response_Model_User $value)
 */
class InstagramAPI_Response_Model_Reel extends InstagramAPI_AutoPropertyHandler {
    // NOTE: We must use full paths to all model objects in THIS class, because
    // "UserReelMediaFeedResponse" re-uses this object and JSONMapper won't be
    // able to find these sub-objects if the paths aren't absolute!

    /**
     * @var string
     */
    public $id;

    /**
     * @var InstagramAPI_Response_Model_Item[]
     */
    public $items;

    /**
     * @var InstagramAPI_Response_Model_User
     */
    public $user;
    public $expiring_at;
    public $seen;
    public $can_reply;

    /**
     * @var InstagramAPI_Response_Model_Location
     */
    public $location;
    public $latest_reel_media;
    public $prefetch_count;

    /**
     * @var InstagramAPI_Response_Model_Broadcast
     */
    public $broadcast;

}
