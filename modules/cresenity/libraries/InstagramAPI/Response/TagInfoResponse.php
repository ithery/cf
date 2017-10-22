<?php

/**
 * @method mixed getMediaCount()
 * @method mixed getProfile()
 * @method bool isMediaCount()
 * @method bool isProfile()
 * @method setMediaCount(mixed $value)
 * @method setProfile(mixed $value)
 */
class InstagramAPI_Response_TagInfoResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $profile;
    public $media_count;

}
