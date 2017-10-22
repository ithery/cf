<?php

/**
 * @method InstagramAPI_Response_Model_Item getMedia()
 * @method bool isMedia()
 * @method setMedia(InstagramAPI_Response_Model_Item $value)
 */
class InstagramAPI_Response_EditMediaResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_Item
     */
    public $media;

}
