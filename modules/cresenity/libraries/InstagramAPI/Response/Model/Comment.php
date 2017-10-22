<?php

/**
 * @method mixed getBitFlags()
 * @method mixed getCommentLikeCount()
 * @method mixed getContentType()
 * @method mixed getCreatedAt()
 * @method mixed getCreatedAtUtc()
 * @method mixed getDidReportAsSpam()
 * @method mixed getHasLikedComment()
 * @method mixed getHasTranslation()
 * @method string getMediaId()
 * @method string getPk()
 * @method mixed getStatus()
 * @method mixed getText()
 * @method mixed getType()
 * @method InstagramAPI_Response_Model_User getUser()
 * @method string getUserId()
 * @method bool isBitFlags()
 * @method bool isCommentLikeCount()
 * @method bool isContentType()
 * @method bool isCreatedAt()
 * @method bool isCreatedAtUtc()
 * @method bool isDidReportAsSpam()
 * @method bool isHasLikedComment()
 * @method bool isHasTranslation()
 * @method bool isMediaId()
 * @method bool isPk()
 * @method bool isStatus()
 * @method bool isText()
 * @method bool isType()
 * @method bool isUser()
 * @method bool isUserId()
 * @method setBitFlags(mixed $value)
 * @method setCommentLikeCount(mixed $value)
 * @method setContentType(mixed $value)
 * @method setCreatedAt(mixed $value)
 * @method setCreatedAtUtc(mixed $value)
 * @method setDidReportAsSpam(mixed $value)
 * @method setHasLikedComment(mixed $value)
 * @method setHasTranslation(mixed $value)
 * @method setMediaId(string $value)
 * @method setPk(string $value)
 * @method setStatus(mixed $value)
 * @method setText(mixed $value)
 * @method setType(mixed $value)
 * @method setUser(InstagramAPI_Response_Model_User $value)
 * @method setUserId(string $value)
 */
class InstagramAPI_Response_Model_Comment extends InstagramAPI_AutoPropertyHandler {

    public $status;

    /**
     * @var string
     */
    public $user_id;
    public $created_at_utc;
    public $created_at;
    public $bit_flags;

    /**
     * @var InstagramAPI_Response_Model_User
     */
    public $user;

    /**
     * @var string
     */
    public $pk;

    /**
     * @var string
     */
    public $media_id;
    public $text;
    public $content_type;
    public $type;
    public $comment_like_count;
    public $has_liked_comment;
    public $has_translation;
    public $did_report_as_spam;

}
