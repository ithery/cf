<?php

/**
 * @method InstagramAPI_Response_Model_BroadcastStatusItem[] getBroadcastStatusItems()
 * @method bool isBroadcastStatusItems()
 * @method setBroadcastStatusItems(InstagramAPI_Response_Model_BroadcastStatusItem[] $value)
 */
class InstagramAPI_Response_TopLiveStatusResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_BroadcastStatusItem[]
     */
    public $broadcast_status_items;

}
