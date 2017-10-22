<?php

/**
 * @method mixed getAutoLoadMoreEnabled()
 * @method InstagramAPI_Response_Model_Item[] getItems()
 * @method mixed getMoreAvailable()
 * @method mixed getNewPhotos()
 * @method string getNextMaxId()
 * @method mixed getNumResults()
 * @method mixed getRequiresReview()
 * @method mixed getTotalCount()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isItems()
 * @method bool isMoreAvailable()
 * @method bool isNewPhotos()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method bool isRequiresReview()
 * @method bool isTotalCount()
 * @method setAutoLoadMoreEnabled(mixed $value)
 * @method setItems(InstagramAPI_Response_Model_Item[] $value)
 * @method setMoreAvailable(mixed $value)
 * @method setNewPhotos(mixed $value)
 * @method setNextMaxId(string $value)
 * @method setNumResults(mixed $value)
 * @method setRequiresReview(mixed $value)
 * @method setTotalCount(mixed $value)
 */
class InstagramAPI_Response_UsertagsResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $num_results;
    public $auto_load_more_enabled;

    /**
     * @var InstagramAPI_Response_Model_Item[]
     */
    public $items;
    public $more_available;

    /**
     * @var string
     */
    public $next_max_id;
    public $total_count;
    public $requires_review;
    public $new_photos;

}
