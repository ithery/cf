<?php

/**
 * @method string getChannelId()
 * @method mixed getChannelType()
 * @method mixed getContext()
 * @method mixed getHeader()
 * @method InstagramAPI_Response_Model_Item getMedia()
 * @method mixed getMediaCount()
 * @method mixed getTitle()
 * @method bool isChannelId()
 * @method bool isChannelType()
 * @method bool isContext()
 * @method bool isHeader()
 * @method bool isMedia()
 * @method bool isMediaCount()
 * @method bool isTitle()
 * @method setChannelId(string $value)
 * @method setChannelType(mixed $value)
 * @method setContext(mixed $value)
 * @method setHeader(mixed $value)
 * @method setMedia(InstagramAPI_Response_Model_Item $value)
 * @method setMediaCount(mixed $value)
 * @method setTitle(mixed $value)
 */
class InstagramAPI_Response_Model_Channel extends InstagramAPI_AutoPropertyHandler
{
    /**
     * @var string
     */
    public $channel_id;
    public $channel_type;
    public $title;
    public $header;
    public $media_count;
    /**
     * @var InstagramAPI_Response_Model_Item
     */
    public $media;
    public $context;
}
