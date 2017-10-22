<?php

/**
 * @method InstagramAPI_Response_Model_StaticStickers[] getStaticStickers()
 * @method mixed getVersion()
 * @method bool isStaticStickers()
 * @method bool isVersion()
 * @method setStaticStickers(InstagramAPI_Response_Model_StaticStickers[] $value)
 * @method setVersion(mixed $value)
 */
class InstagramAPI_StickerAssetsResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $version;

    /**
     * @var InstagramAPI_Response_Model_StaticStickers[]
     */
    public $static_stickers;

}
