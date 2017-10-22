<?php

/**
 * @method mixed getAutoLoadMoreEnabled()
 * @method InstagramAPI_Response_Model_SavedFeedItem[] getItems()
 * @method mixed getMoreAvailable()
 * @method string getNextMaxId()
 * @method mixed getNumResults()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isItems()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method setAutoLoadMoreEnabled(mixed $value)
 * @method setItems(InstagramAPI_Response_Model_SavedFeedItem[] $value)
 * @method setMoreAvailable(mixed $value)
 * @method setNextMaxId(string $value)
 * @method setNumResults(mixed $value)
 */
class InstagramAPI_Response_SavedFeedResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_SavedFeedItem[]
     */
    public $items;
    public $more_available;

    /**
     * @var string
     */
    public $next_max_id;
    public $auto_load_more_enabled;
    public $num_results;

}
