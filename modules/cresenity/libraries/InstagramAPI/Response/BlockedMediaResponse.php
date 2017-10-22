<?php

/**
 * @method mixed getMediaIds()
 * @method bool isMediaIds()
 * @method setMediaIds(mixed $value)
 */
class InstagramAPI_Response_BlockedMediaResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $media_ids;

}
