<?php

/**
 * @method mixed getBroadcasts()
 * @method InstagramAPI_Response_Model_Item getMedia()
 * @method mixed getStickerVersion()
 * @method mixed getStoryRankingToken()
 * @method mixed getText()
 * @method InstagramAPI_Response_Model_Item[] getTray()
 * @method mixed getType()
 * @method bool isBroadcasts()
 * @method bool isMedia()
 * @method bool isStickerVersion()
 * @method bool isStoryRankingToken()
 * @method bool isText()
 * @method bool isTray()
 * @method bool isType()
 * @method setBroadcasts(mixed $value)
 * @method setMedia(InstagramAPI_Response_Model_Item $value)
 * @method setStickerVersion(mixed $value)
 * @method setStoryRankingToken(mixed $value)
 * @method setText(mixed $value)
 * @method setTray(InstagramAPI_Response_Model_Item[] $value)
 * @method setType(mixed $value)
 */
class InstagramAPI_Response_Model_ReelShare extends InstagramAPI_AutoPropertyHandler {

    /**
     * @var InstagramAPI_Response_Model_Item[]
     */
    public $tray;
    public $story_ranking_token;
    public $broadcasts;
    public $sticker_version;
    public $text;
    public $type;

    /**
     * @var InstagramAPI_Response_Model_Item
     */
    public $media;

}
