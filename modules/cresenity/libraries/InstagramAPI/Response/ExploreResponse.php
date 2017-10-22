<?php

/**
 * @method mixed getAutoLoadMoreEnabled()
 * @method Model\ExploreItem[] getItems()
 * @method string getMaxId()
 * @method mixed getMoreAvailable()
 * @method string getNextMaxId()
 * @method mixed getNumResults()
 * @method mixed getRankToken()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isItems()
 * @method bool isMaxId()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method bool isRankToken()
 * @method setAutoLoadMoreEnabled(mixed $value)
 * @method setItems(Model\ExploreItem[] $value)
 * @method setMaxId(string $value)
 * @method setMoreAvailable(mixed $value)
 * @method setNextMaxId(string $value)
 * @method setNumResults(mixed $value)
 * @method setRankToken(mixed $value)
 */
class InstagramAPI_Response_ExploreResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $num_results;
    public $auto_load_more_enabled;

    /**
     * @var InstagramAPI_Response_Model_ExploreItem[]
     */
    public $items;
    public $more_available;

    /**
     * @var string
     */
    public $next_max_id;

    /**
     * @var string
     */
    public $max_id;
    public $rank_token;

}
