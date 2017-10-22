<?php

/**
 * @method InstagramAPI_Response_Model_Location getLocation()
 * @method mixed getMediaBundles()
 * @method mixed getSubtitle()
 * @method mixed getTitle()
 * @method bool isLocation()
 * @method bool isMediaBundles()
 * @method bool isSubtitle()
 * @method bool isTitle()
 * @method setLocation(InstagramAPI_Response_Model_Location $value)
 * @method setMediaBundles(mixed $value)
 * @method setSubtitle(mixed $value)
 * @method setTitle(mixed $value)
 */
class InstagramAPI_Response_Model_LocationItem extends InstagramAPI_AutoPropertyHandler {

    public $media_bundles;
    public $subtitle;

    /**
     * @var InstagramAPI_Response_Model_Location
     */
    public $location;
    public $title;

}
