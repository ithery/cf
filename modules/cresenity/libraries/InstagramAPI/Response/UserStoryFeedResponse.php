<?php

/**
 * @method InstagramAPI_Response_Model_Broadcast getBroadcast()
 * @method InstagramAPI_Response_Model_PostLiveItem getPostLiveItem()
 * @method InstagramAPI_Response_Model_Reel getReel()
 * @method bool isBroadcast()
 * @method bool isPostLiveItem()
 * @method bool isReel()
 * @method setBroadcast(InstagramAPI_Response_Model_Broadcast $value)
 * @method setPostLiveItem(InstagramAPI_Response_Model_PostLiveItem $value)
 * @method setReel(InstagramAPI_Response_Model_Reel $value)
 */
class InstagramAPI_Response_UserStoryFeedResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_Broadcast
     */
    public $broadcast;

    /**
     * @var InstagramAPI_Response_Model_Reel
     */
    public $reel;

    /**
     * @var InstagramAPI_Response_Model_PostLiveItem
     */
    public $post_live_item;

}
