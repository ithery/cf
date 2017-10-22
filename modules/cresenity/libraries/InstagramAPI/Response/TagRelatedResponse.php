<?php

/**
 * @method InstagramAPI_Response_Model_Related[] getRelated()
 * @method bool isRelated()
 * @method setRelated(InstagramAPI_Response_Model_Related[] $value)
 */
class InstagramAPI_Response_TagRelatedResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_Related[]
     */
    public $related;

}
