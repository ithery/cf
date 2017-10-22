<?php


/**
 * @method InstagramAPI_Response_Model_ImageVersions2 getImageVersions2()
 * @method mixed getMediaType()
 * @method mixed getOriginalHeight()
 * @method mixed getOriginalWidth()
 * @method InstagramAPI_Response_Model_VideoVersions[] getVideoVersions()
 * @method bool isImageVersions2()
 * @method bool isMediaType()
 * @method bool isOriginalHeight()
 * @method bool isOriginalWidth()
 * @method bool isVideoVersions()
 * @method setImageVersions2(InstagramAPI_Response_Model_Image_Versions2 $value)
 * @method setMediaType(mixed $value)
 * @method setOriginalHeight(mixed $value)
 * @method setOriginalWidth(mixed $value)
 * @method setVideoVersions(InstagramAPI_Response_Model_VideoVersions[] $value)
 */
class InstagramAPI_Response_Model_DirectThreadItemMedia extends InstagramAPI_AutoPropertyHandler {

    const PHOTO = 1;
    const VIDEO = 2;

    public $media_type;

    /**
     * @var InstagramAPI_Response_Model_ImageVersions2
     */
    public $image_versions2;

    /**
     * @var InstagramAPI_Response_Model_VideoVersions[]
     */
    public $video_versions;
    public $original_width;
    public $original_height;

}
