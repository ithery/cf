<?php

/**
 * @method float getExpires()
 * @method string getJob()
 * @method string getUrl()
 * @method bool isExpires()
 * @method bool isJob()
 * @method bool isUrl()
 * @method setExpires(float $value)
 * @method setJob(string $value)
 * @method setUrl(string $value)
 */
class InstagramAPI_Response_Model_VideoUploadUrl extends InstagramAPI_AutoPropertyHandler {

    /** @var string */
    public $url;

    /** @var string */
    public $job;

    /** @var float */
    public $expires;

}
