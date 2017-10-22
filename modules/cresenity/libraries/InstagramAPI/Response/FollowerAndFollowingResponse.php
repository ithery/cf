<?php

namespace InstagramAPI\Response;

use InstagramAPI\AutoPropertyHandler;
use InstagramAPI\ResponseInterface;
use InstagramAPI\ResponseTrait;

/**
 * @method mixed getBigList()
 * @method string getNextMaxId()
 * @method mixed getPageSize()
 * @method InstagramAPI_Response_Model_User[] getUsers()
 * @method bool isBigList()
 * @method bool isNextMaxId()
 * @method bool isPageSize()
 * @method bool isUsers()
 * @method setBigList(mixed $value)
 * @method setNextMaxId(string $value)
 * @method setPageSize(mixed $value)
 * @method setUsers(InstagramAPI_Response_Model_User[] $value)
 */
class InstagramAPI_Response_FollowerAndFollowingResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_User[]
     */
    public $users;

    /**
     * @var string
     */
    public $next_max_id;
    public $page_size;
    public $big_list;

}
