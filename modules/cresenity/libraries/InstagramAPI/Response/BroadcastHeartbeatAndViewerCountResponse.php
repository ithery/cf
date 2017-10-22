<?php

/**
 * @method mixed getBroadcastStatus()
 * @method mixed getViewerCount()
 * @method bool isBroadcastStatus()
 * @method bool isViewerCount()
 * @method setBroadcastStatus(mixed $value)
 * @method setViewerCount(mixed $value)
 */
class InstagramAPI_Response_BroadcastHeartbeatAndViewerCountResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $broadcast_status;
    public $viewer_count;

}
