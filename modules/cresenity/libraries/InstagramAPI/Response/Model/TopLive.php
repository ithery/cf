<?php

/**
 * @method InstagramAPI_Response_Model_BroadcastOwner[] getBroadcastOwners()
 * @method mixed getRankedPosition()
 * @method bool isBroadcastOwners()
 * @method bool isRankedPosition()
 * @method setBroadcastOwners(InstagramAPI_Response_Model_BroadcastOwner[] $value)
 * @method setRankedPosition(mixed $value)
 */
class InstagramAPI_Response_Model_TopLive extends InstagramAPI_AutoPropertyHandler {

    /**
     * @var InstagramAPI_Response_Model_BroadcastOwner[]
     */
    public $broadcast_owners;
    public $ranked_position;

}
