<?php

/**
 * @method InstagramAPI_Response_Model_Broadcast[] getBroadcasts()
 * @method mixed getFaceFilterNuxVersion()
 * @method InstagramAPI_Response_Model_PostLive getPostLive()
 * @method mixed getStickerVersion()
 * @method mixed getStoryRankingToken()
 * @method InstagramAPI_Response_Model_StoryTray[] getTray()
 * @method bool isBroadcasts()
 * @method bool isFaceFilterNuxVersion()
 * @method bool isPostLive()
 * @method bool isStickerVersion()
 * @method bool isStoryRankingToken()
 * @method bool isTray()
 * @method setBroadcasts(InstagramAPI_Response_Model_Broadcast[] $value)
 * @method setFaceFilterNuxVersion(mixed $value)
 * @method setPostLive(InstagramAPI_Response_Model_PostLive $value)
 * @method setStickerVersion(mixed $value)
 * @method setStoryRankingToken(mixed $value)
 * @method setTray(InstagramAPI_Response_Model_StoryTray[] $value)
 */
class InstagramAPI_Response_ReelsTrayFeedResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_StoryTray[]
     */
    public $tray;

    /**
     * @var InstagramAPI_Response_Model_Broadcast[]
     */
    public $broadcasts;

    /**
     * @var InstagramAPI_Response_Model_PostLive
     */
    public $post_live;
    public $sticker_version;
    public $face_filter_nux_version;
    public $story_ranking_token;

}
