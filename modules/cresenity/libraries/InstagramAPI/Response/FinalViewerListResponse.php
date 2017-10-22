<?php

/**
 * @method mixed getTotalUniqueViewerCount()
 * @method InstagramAPI_Response_Model_User[] getUsers()
 * @method bool isTotalUniqueViewerCount()
 * @method bool isUsers()
 * @method setTotalUniqueViewerCount(mixed $value)
 * @method setUsers(InstagramAPI_Response_Model_User[] $value)
 */
class InstagramAPI_Response_FinalViewerListResponse extends InstagramAPI_AutoPropertyHandler implements InstragramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_User[]
     */
    public $users;
    public $total_unique_viewer_count;

}
