<?php

/**
 * @method mixed getIsBackup()
 * @method InstagramAPI_Response_Model_User[] getUsers()
 * @method bool isIsBackup()
 * @method bool isUsers()
 * @method setIsBackup(mixed $value)
 * @method setUsers(InstagramAPI_Response_Model_User[] $value)
 */
class InstagramAPI_Response_SuggestedUsersResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_User[]
     */
    public $users;
    public $is_backup;

}
