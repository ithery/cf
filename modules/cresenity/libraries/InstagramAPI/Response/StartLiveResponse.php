<?php

/**
 * @method string getMediaId()
 * @method bool isMediaId()
 * @method setMediaId(string $value)
 */
class InstagramAPI_Response_StartLiveResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var string
     */
    public $media_id;

}
