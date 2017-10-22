<?php

/**
 * @method mixed getCanReply()
 * @method mixed getCanReshare()
 * @method InstagramAPI_Response_Model_DismissCard getDismissCard()
 * @method mixed getExpiringAt()
 * @method mixed getHasBestiesMedia()
 * @method string getId()
 * @method mixed getIsNux()
 * @method InstagramAPI_Response_Model_Item[] getItems()
 * @method mixed getLatestReelMedia()
 * @method Location getLocation()
 * @method mixed getMuted()
 * @method string getNuxId()
 * @method InstagramAPI_Response_Model_Owner getOwner()
 * @method mixed getPrefetchCount()
 * @method mixed getRankedPosition()
 * @method mixed getSeen()
 * @method mixed getSeenRankedPosition()
 * @method mixed getShowNuxTooltip()
 * @method mixed getSourceToken()
 * @method InstagramAPI_Response_Model_User getUser()
 * @method bool isCanReply()
 * @method bool isCanReshare()
 * @method bool isDismissCard()
 * @method bool isExpiringAt()
 * @method bool isHasBestiesMedia()
 * @method bool isId()
 * @method bool isIsNux()
 * @method bool isItems()
 * @method bool isLatestReelMedia()
 * @method bool isLocation()
 * @method bool isMuted()
 * @method bool isNuxId()
 * @method bool isOwner()
 * @method bool isPrefetchCount()
 * @method bool isRankedPosition()
 * @method bool isSeen()
 * @method bool isSeenRankedPosition()
 * @method bool isShowNuxTooltip()
 * @method bool isSourceToken()
 * @method bool isUser()
 * @method setCanReply(mixed $value)
 * @method setCanReshare(mixed $value)
 * @method setDismissCard(DismissCard $value)
 * @method setExpiringAt(mixed $value)
 * @method setHasBestiesMedia(mixed $value)
 * @method setId(string $value)
 * @method setIsNux(mixed $value)
 * @method setItems(InstagramAPI_Response_Model_Item[] $value)
 * @method setLatestReelMedia(mixed $value)
 * @method setLocation(Location $value)
 * @method setMuted(mixed $value)
 * @method setNuxId(string $value)
 * @method setOwner(InstagramAPI_Response_Model_Owner $value)
 * @method setPrefetchCount(mixed $value)
 * @method setRankedPosition(mixed $value)
 * @method setSeen(mixed $value)
 * @method setSeenRankedPosition(mixed $value)
 * @method setShowNuxTooltip(mixed $value)
 * @method setSourceToken(mixed $value)
 * @method setUser(InstagramAPI_Response_Model_User $value)
 */
class InstagramAPI_Response_Model_StoryTray extends InstagramAPI_AutoPropertyHandler {

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
    public $can_reply;
    public $expiring_at;
    public $seen_ranked_position;
    public $seen;
    public $latest_reel_media;
    public $ranked_position;
    public $is_nux;
    public $show_nux_tooltip;
    public $muted;
    public $prefetch_count;

    /**
     * @var InstagramAPI_Response_Model_Location
     */
    public $location;
    public $source_token;

    /**
     * @var InstagramAPI_Response_Model_Owner
     */
    public $owner;

    /**
     * @var string
     */
    public $nux_id;

    /**
     * @var InstagramAPI_Response_Model_DismissCard
     */
    public $dismiss_card;
    public $can_reshare;
    public $has_besties_media;

}
