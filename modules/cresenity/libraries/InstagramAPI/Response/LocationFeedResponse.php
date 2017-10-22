<?php

/**
 * @method mixed getAutoLoadMoreEnabled()
 * @method InstagramAPI_Response_Model_Item[] getItems()
 * @method InstagramAPI_Response_Model_Location getLocation()
 * @method mixed getMediaCount()
 * @method mixed getMoreAvailable()
 * @method string getNextMaxId()
 * @method mixed getNumResults()
 * @method InstagramAPI_Response_Model_Item[] getRankedItems()
 * @method InstagramAPI_Response_Model_StoryTray getStory()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isItems()
 * @method bool isLocation()
 * @method bool isMediaCount()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method bool isRankedItems()
 * @method bool isStory()
 * @method setAutoLoadMoreEnabled(mixed $value)
 * @method setItems(InstagramAPI_Response_Model_Item[] $value)
 * @method setLocation(InstagramAPI_Response_Model_Location $value)
 * @method setMediaCount(mixed $value)
 * @method setMoreAvailable(mixed $value)
 * @method setNextMaxId(string $value)
 * @method setNumResults(mixed $value)
 * @method setRankedItems(InstagramAPI_Response_Model_Item[] $value)
 * @method setStory(InstagramAPI_Response_Model_StoryTray $value)
 */
class InstagramAPI_Response_LocationFeedResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $media_count;
    public $num_results;
    public $auto_load_more_enabled;

    /**
     * @var InstagramAPI_Response_Model_Item[]
     */
    public $items;

    /**
     * @var InstagramAPI_Response_Model_Item[]
     */
    public $ranked_items;
    public $more_available;

    /**
     * @var InstagramAPI_Response_Model_StoryTray
     */
    public $story;

    /**
     * @var InstagramAPI_Response_Model_Location
     */
    public $location;

    /**
     * @var string
     */
    public $next_max_id;

}
