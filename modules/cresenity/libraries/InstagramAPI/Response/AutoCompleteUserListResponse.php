<?php

/**
 * @method mixed getExpires()
 * @method InstagramAPI_Response_Model_User[] getUsers()
 * @method bool isExpires()
 * @method bool isUsers()
 * @method setExpires(mixed $value)
 * @method setUsers(InstagramAPI_Response_Model_User[] $value)
 */
class InstagramAPI_Response_AutoCompleteUserListResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $expires;

    /**
     * @var InstagramAPI_Response_Model_User[]
     */
    public $users;

}
