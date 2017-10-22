<?php

/**
 * @method InstagramAPI_Response_Model_Channel getChannel()
 * @method InstagramAPI_Response_Model_Item getMedia()
 * @method InstagramAPI_Response_Model_Stories getStories()
 * @method bool isChannel()
 * @method bool isMedia()
 * @method bool isStories()
 * @method setChannel(InstagramAPI_Response_Model_Channel $value)
 * @method setMedia(InstagramAPI_Response_Model_Item $value)
 * @method setStories(InstagramAPI_Response_Model_Stories $value)
 */
class InstagramAPI_Response_Model_ExploreItem extends InstagramAPI_AutoPropertyHandler {

    /**
     * @var InstagramAPI_Response_Model_Item
     */
    public $media;

    /**
     * @var InstagramAPI_Response_Model_Stories
     */
    public $stories;

    /**
     * @var InstagramAPI_Response_Model_Channel
     */
    public $channel;

}
