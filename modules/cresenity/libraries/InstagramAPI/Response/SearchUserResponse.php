<?php

/**
 * @method mixed getHasMore()
 * @method string getNextMaxId()
 * @method mixed getNumResults()
 * @method mixed getRankToken()
 * @method InstagramAPI_Response_Model_User[] getUsers()
 * @method bool isHasMore()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method bool isRankToken()
 * @method bool isUsers()
 * @method setHasMore(mixed $value)
 * @method setNextMaxId(string $value)
 * @method setNumResults(mixed $value)
 * @method setRankToken(mixed $value)
 * @method setUsers(InstagramAPI_Response_Model_User[] $value)
 */
class InstagramAPI_Response_SearchUserResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $has_more;
    public $num_results;

    /**
     * @var string
     */
    public $next_max_id;

    /**
     * @var InstagramAPI_Response_Model_User[]
     */
    public $users;
    public $rank_token;

}
