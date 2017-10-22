<?php

/**
 * @method InstagramAPI_Response_Model_CommentTranslations[] getCommentTranslations()
 * @method bool isCommentTranslations()
 * @method setCommentTranslations(InstagramAPI_Response_Model_CommentTranslations[] $value)
 */
class InstagramAPI_Response_TranslateResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_CommentTranslations[]
     */
    public $comment_translations;

}
