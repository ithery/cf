<?php

/**
 * @method mixed getRankToken()
 * @method InstagramAPI_Response_Model_Suggested[] getSuggested()
 * @method bool isRankToken()
 * @method bool isSuggested()
 * @method setRankToken(mixed $value)
 * @method setSuggested(InstagramAPI_Response_Model_Suggested[] $value)
 */
class InstagramAPI_Response_SuggestedUsersFacebookResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_Suggested[]
     */
    public $suggested;
    public $rank_token;

}
