<?php


/**
 * @method InstagramAPI_Response_Model_ActionLog getActionLog()
 * @method mixed getClientContext()
 * @method InstagramAPI_Response_Model_DirectExpiringSummary getExpiringMediaActionSummary()
 * @method mixed getHideInThread()
 * @method string getItemId()
 * @method mixed getItemType()
 * @method mixed getLike()
 * @method InstagramAPI_Response_Model_DirectLink getLink()
 * @method mixed getLiveVideoShare()
 * @method InstagramAPI_Response_Model_Location getLocation()
 * @method InstagramAPI_Response_Model_DirectThreadItemMedia getMedia()
 * @method InstagramAPI_Response_Model_Item getMediaShare()
 * @method InstagramAPI_Response_Model_Placeholder getPlaceholder()
 * @method InstagramAPI_Response_Model_Item getRavenMedia()
 * @method InstagramAPI_Response_Model_DirectReactions getReactions()
 * @method InstagramAPI_Response_Model_ReelShare getReelShare()
 * @method array getSeenUserIds()
 * @method mixed getText()
 * @method mixed getTimestamp()
 * @method string getUserId()
 * @method bool isActionLog()
 * @method bool isClientContext()
 * @method bool isExpiringMediaActionSummary()
 * @method bool isHideInThread()
 * @method bool isItemId()
 * @method bool isItemType()
 * @method bool isLike()
 * @method bool isLink()
 * @method bool isLiveVideoShare()
 * @method bool isLocation()
 * @method bool isMedia()
 * @method bool isMediaShare()
 * @method bool isPlaceholder()
 * @method bool isRavenMedia()
 * @method bool isReactions()
 * @method bool isReelShare()
 * @method bool isSeenUserIds()
 * @method bool isText()
 * @method bool isTimestamp()
 * @method bool isUserId()
 * @method setActionLog(InstagramAPI_Response_Model_ActionLog $value)
 * @method setClientContext(mixed $value)
 * @method setExpiringMediaActionSummary(InstagramAPI_Response_Model_DirectExpiringSummary $value)
 * @method setHideInThread(mixed $value)
 * @method setItemId(string $value)
 * @method setItemType(mixed $value)
 * @method setLike(mixed $value)
 * @method setLink(InstagramAPI_Response_Model_DirectLink $value)
 * @method setLiveVideoShare(mixed $value)
 * @method setLocation(InstagramAPI_Response_Model_Location $value)
 * @method setMedia(InstagramAPI_Response_Model_DirectThreadItemMedia $value)
 * @method setMediaShare(InstagramAPI_Response_Model_Item $value)
 * @method setPlaceholder(InstagramAPI_Response_Model_Placeholder $value)
 * @method setRavenMedia(InstagramAPI_Response_Model_Item $value)
 * @method setReactions(InstagramAPI_Response_Model_DirectReactions $value)
 * @method setReelShare(InstagramAPI_Response_Model_ReelShare $value)
 * @method setSeenUserIds(array $value)
 * @method setText(mixed $value)
 * @method setTimestamp(mixed $value)
 * @method setUserId(string $value)
 */
class InstagramAPI_Response_Model_DirectThreadItem extends InstagramAPI_AutoPropertyHandler {

    const PLACEHOLDER = 'placeholder';
    const TEXT = 'text';
    const HASHTAG = 'hashtag';
    const LOCATION = 'location';
    const PROFILE = 'profile';
    const MEDIA = 'media';
    const MEDIA_SHARE = 'media_share';
    const EXPIRING_MEDIA = 'raven_media';
    const LIKE = 'like';
    const ACTION_LOG = 'action_log';
    const REACTION = 'reaction';
    const REEL_SHARE = 'reel_share';
    const LINK = 'link';

    /**
     * @var string
     */
    public $item_id;
    public $item_type;
    public $text;

    /**
     * @var InstagramAPI_Response_Model_Item
     */
    public $media_share;

    /**
     * @var InstagramAPI_Response_Model_DirectThreadItemMedia
     */
    public $media;

    /**
     * @var string
     */
    public $user_id;
    public $timestamp;
    public $client_context;
    public $hide_in_thread;

    /**
     * @var InstagramAPI_Response_Model_ActionLog
     */
    public $action_log;

    /**
     * @var InstagramAPI_Response_Model_DirectLink
     */
    public $link;

    /**
     * @var InstagramAPI_Response_Model_DirectReactions
     */
    public $reactions;

    /**
     * @var InstagramAPI_Response_Model_Item
     */
    public $raven_media;

    /**
     * @var array
     */
    public $seen_user_ids;

    /**
     * @var InstagramAPI_Response_Model_DirectExpiringSummary
     */
    public $expiring_media_action_summary;

    /**
     * @var InstagramAPI_Response_Model_ReelShare
     */
    public $reel_share;

    /**
     * @var InstagramAPI_Response_Model_Placeholder
     */
    public $placeholder;

    /**
     * @var InstagramAPI_Response_Model_Location
     */
    public $location;
    public $like;
    public $live_video_share;

}
