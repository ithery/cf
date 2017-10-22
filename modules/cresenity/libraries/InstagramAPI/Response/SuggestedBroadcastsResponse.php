<?php

/**
 * @method InstagramAPI_Response_Model_Broadcast[] getBroadcasts()
 * @method bool isBroadcasts()
 * @method setBroadcasts(InstagramAPI_Response_Model_Broadcast[] $value)
 */
class InstagramAPI_Response_SuggestedBroadcastsResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_Broadcast[]
     */
    public $broadcasts;

}
