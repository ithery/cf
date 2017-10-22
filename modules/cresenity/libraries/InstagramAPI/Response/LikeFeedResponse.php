<?php

/**
 * @method mixed getAutoLoadMoreEnabled()
 * @method InstagramAPI_Response_Model_Item[] getItems()
 * @method mixed getLastCountedAt()
 * @method mixed getMoreAvailable()
 * @method string getNextMaxId()
 * @method mixed getNumResults()
 * @method mixed getPatches()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isItems()
 * @method bool isLastCountedAt()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method bool isPatches()
 * @method setAutoLoadMoreEnabled(mixed $value)
 * @method setItems(InstagramAPI_Response_Model_Item[] $value)
 * @method setLastCountedAt(mixed $value)
 * @method setMoreAvailable(mixed $value)
 * @method setNextMaxId(string $value)
 * @method setNumResults(mixed $value)
 * @method setPatches(mixed $value)
 */
class InstagramAPI_Response_LikeFeedResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $auto_load_more_enabled;

    /**
     * @var InstagramAPI_Response_Model_Item[]
     */
    public $items;
    public $more_available;
    public $patches;
    public $last_counted_at;
    public $num_results;

    /**
     * @var string
     */
    public $next_max_id;

}
