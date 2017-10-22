<?php

/**
 * @method mixed getUserCount()
 * @method InstagramAPI_Response_Model_User[] getUsers()
 * @method bool isUserCount()
 * @method bool isUsers()
 * @method setUserCount(mixed $value)
 * @method setUsers(InstagramAPI_Response_Model_User[] $value)
 */
class InstagramAPI_Response_MediaLikersResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $user_count;

    /**
     * @var InstagramAPI_Response_Model_User[]
     */
    public $users;

}
