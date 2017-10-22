<?php

/**
 * @method mixed getExpiringAt()
 * @method string getId()
 * @method mixed getImage()
 * @method InstagramAPI_Response_Model_User getUser()
 * @method bool isExpiringAt()
 * @method bool isId()
 * @method bool isImage()
 * @method bool isUser()
 * @method setExpiringAt(mixed $value)
 * @method setId(string $value)
 * @method setImage(mixed $value)
 * @method setUser(InstagramAPI_Response_Model_User $value)
 */
class InstagramAPI_Response_Model_Media extends InstagramAPI_AutoPropertyHandler {

    public $image;

    /**
     * @var string
     */
    public $id;

    /**
     * @var InstagramAPI_Response_Model_User
     */
    public $user;
    public $expiring_at;

}
