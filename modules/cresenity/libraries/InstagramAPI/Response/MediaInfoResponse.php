<?php

/**
 * @method mixed getAutoLoadMoreEnabled()
 * @method InstagramAPI_Response_Model_Item[] getItems()
 * @method mixed getMoreAvailable()
 * @method mixed getNumResults()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isItems()
 * @method bool isMoreAvailable()
 * @method bool isNumResults()
 * @method setAutoLoadMoreEnabled(mixed $value)
 * @method setItems(InstagramAPI_Response_Model_Item[] $value)
 * @method setMoreAvailable(mixed $value)
 * @method setNumResults(mixed $value)
 */
class InstagramAPI_Response_MediaInfoResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $auto_load_more_enabled;
    public $num_results;
    public $more_available;

    /**
     * @var InstagramAPI_Response_Model_Item[]
     */
    public $items;

}
