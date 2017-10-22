<?php

/**
 * @method string getNextMaxId()
 * @method bool isNextMaxId()
 * @method setNextMaxId(string $value)
 */
class InstagramAPI_BlockedReelsResponse extends InstagramAPI_Response_Model_BlockedReels implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    // NOTE: This is a special response object which extends
    // InstagramAPI_Response_Model_BlockedReels to inherit all of its properties!

    /**
     * @var string
     */
    public $next_max_id;

}
