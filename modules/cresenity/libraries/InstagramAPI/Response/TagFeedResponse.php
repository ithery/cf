<?php

/**
 * @method mixed getAutoLoadMoreEnabled()
 * @method InstagramAPI_Response_Model_Item[] getItems()
 * @method mixed getMoreAvailable()
 * @method string getNextMaxId()
 * @method mixed getNumResults()
 * @method InstagramAPI_Response_Model_Item[] getRankedItems()
 * @method InstagramAPI_Response_Model_StoryTray getStory()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isItems()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method bool isRankedItems()
 * @method bool isStory()
 * @method setAutoLoadMoreEnabled(mixed $value)
 * @method setItems(InstagramAPI_Response_Model_Item[] $value)
 * @method setMoreAvailable(mixed $value)
 * @method setNextMaxId(string $value)
 * @method setNumResults(mixed $value)
 * @method setRankedItems(InstagramAPI_Response_Model_Item[] $value)
 * @method setStory(InstagramAPI_Response_Model_StoryTray $value)
 */
class InstagramAPI_Response_TagFeedResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $num_results;

    /**
     * @var InstagramAPI_Response_Model_Item[]
     */
    public $ranked_items;
    public $auto_load_more_enabled;

    /**
     * @var InstagramAPI_Response_Model_Item[]
     */
    public $items;

    /**
     * @var InstagramAPI_Response_Model_StoryTray
     */
    public $story;
    public $more_available;

    /**
     * @var string
     */
    public $next_max_id;

}
