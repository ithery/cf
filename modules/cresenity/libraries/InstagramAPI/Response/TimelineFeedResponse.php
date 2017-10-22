<?php

/**
 * @method mixed getAutoLoadMoreEnabled()
 * @method InstagramAPI_Response_Model_FeedItem[] getFeedItems()
 * @method mixed getIsDirectV2Enabled()
 * @method InstagramAPI_Response_Model_FeedAysf getMegaphone()
 * @method mixed getMoreAvailable()
 * @method string getNextMaxId()
 * @method mixed getNumResults()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isFeedItems()
 * @method bool isIsDirectV2Enabled()
 * @method bool isMegaphone()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method setAutoLoadMoreEnabled(mixed $value)
 * @method setFeedItems(InstagramAPI_Response_Model_FeedItem[] $value)
 * @method setIsDirectV2Enabled(mixed $value)
 * @method setMegaphone(InstagramAPI_Response_Model_FeedAysf $value)
 * @method setMoreAvailable(mixed $value)
 * @method setNextMaxId(string $value)
 * @method setNumResults(mixed $value)
 */
class InstagramAPI_Response_TimelineFeedResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $num_results;
    public $is_direct_v2_enabled;
    public $auto_load_more_enabled;
    public $more_available;

    /**
     * @var string
     */
    public $next_max_id;

    /**
     * @var InstagramAPI_Response_Model_FeedItem[]
     */
    public $feed_items;

    /**
     * @var InstagramAPI_Response_Model_FeedAysf
     */
    public $megaphone;

}
