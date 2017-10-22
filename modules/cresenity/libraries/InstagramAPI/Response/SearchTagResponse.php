<?php

/**
 * @method mixed getHasMore()
 * @method mixed getRankToken()
 * @method InstagramAPI_Response_Model_Tag[] getResults()
 * @method bool isHasMore()
 * @method bool isRankToken()
 * @method bool isResults()
 * @method setHasMore(mixed $value)
 * @method setRankToken(mixed $value)
 * @method setResults(InstagramAPI_Response_Model_Tag[] $value)
 */
class InstagramAPI_Response_SearchTagResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $has_more;

    /**
     * @var InstagramAPI_Response_Model_Tag[]
     */
    public $results;
    public $rank_token;

}
