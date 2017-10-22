<?php

/**
 * @method mixed getActionUrl()
 * @method mixed getClicked()
 * @method string getCommentId()
 * @method string[] getCommentIds()
 * @method mixed getDestination()
 * @method InstagramAPI_Response_Model_InlineFollow getInlineFollow()
 * @method InstagramAPI_Response_Model_Link[] getLinks()
 * @method InstagramAPI_Response_Model_Media[] getMedia()
 * @method string getProfileId()
 * @method mixed getProfileImage()
 * @method mixed getProfileImageDestination()
 * @method mixed getRequestCount()
 * @method string getSecondProfileId()
 * @method mixed getSecondProfileImage()
 * @method mixed getText()
 * @method mixed getTimestamp()
 * @method mixed getTuuid()
 * @method bool isActionUrl()
 * @method bool isClicked()
 * @method bool isCommentId()
 * @method bool isCommentIds()
 * @method bool isDestination()
 * @method bool isInlineFollow()
 * @method bool isLinks()
 * @method bool isMedia()
 * @method bool isProfileId()
 * @method bool isProfileImage()
 * @method bool isProfileImageDestination()
 * @method bool isRequestCount()
 * @method bool isSecondProfileId()
 * @method bool isSecondProfileImage()
 * @method bool isText()
 * @method bool isTimestamp()
 * @method bool isTuuid()
 * @method setActionUrl(mixed $value)
 * @method setClicked(mixed $value)
 * @method setCommentId(string $value)
 * @method setCommentIds(string[] $value)
 * @method setDestination(mixed $value)
 * @method setInlineFollow(InstagramAPI_Response_Model_InlineFollow $value)
 * @method setLinks(InstagramAPI_Response_Model_Link[] $value)
 * @method setMedia(InstagramAPI_Response_Model_Media[] $value)
 * @method setProfileId(string $value)
 * @method setProfileImage(mixed $value)
 * @method setProfileImageDestination(mixed $value)
 * @method setRequestCount(mixed $value)
 * @method setSecondProfileId(string $value)
 * @method setSecondProfileImage(mixed $value)
 * @method setText(mixed $value)
 * @method setTimestamp(mixed $value)
 * @method setTuuid(mixed $value)
 */
class InstagramAPI_Response_Model_Args extends InstagramAPI_AutoPropertyHandler {

    /**
     * @var InstagramAPI_Response_Model_Media[]
     */
    public $media;

    /**
     * @var InstagramAPI_Response_Model_Link[]
     */
    public $links;
    public $text;

    /**
     * @var string
     */
    public $profile_id;
    public $profile_image;
    public $timestamp;

    /**
     * @var string
     */
    public $comment_id;
    public $request_count;
    public $action_url;
    public $destination;

    /**
     * @var InstagramAPI_Response_Model_InlineFollow
     */
    public $inline_follow;

    /**
     * @var string[]
     */
    public $comment_ids;

    /**
     * @var string
     */
    public $second_profile_id;
    public $second_profile_image;
    public $profile_image_destination;
    public $tuuid;
    public $clicked;

}
