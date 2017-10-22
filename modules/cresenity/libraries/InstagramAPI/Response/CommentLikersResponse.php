<?php

/**
 * @method InstagramAPI_Response_Model_User[] getUsers()
 * @method bool isUsers()
 * @method setUsers(InstagramAPI_Response_Model_User[] $value)
 */
class InstagramAPI_Response_CommentLikersResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_User[]
     */
    public $users;

}
