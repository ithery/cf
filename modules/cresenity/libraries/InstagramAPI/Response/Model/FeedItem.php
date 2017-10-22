<?php

/**
 * @method Ad4ad getAd4ad()
 * @method mixed getAdLinkType()
 * @method Item getMediaOrAd()
 * @method SuggestedUsers getSuggestedUsers()
 * @method bool isAd4ad()
 * @method bool isAdLinkType()
 * @method bool isMediaOrAd()
 * @method bool isSuggestedUsers()
 * @method setAd4ad(Ad4ad $value)
 * @method setAdLinkType(mixed $value)
 * @method setMediaOrAd(Item $value)
 * @method setSuggestedUsers(SuggestedUsers $value)
 */
class InstagramAPI_Response_Model_FeedItem extends InstagramAPI_AutoPropertyHandler {

    /**
     * @var InstagramAPI_Response_Model_Item
     */
    public $media_or_ad;

    /**
     * @var InstagramAPI_Response_Model_Ad4ad
     */
    public $ad4ad;

    /**
     * @var InstagramAPI_Response_Model_SuggestedUsers
     */
    public $suggested_users;
    public $ad_link_type;

}
