<?php

/**
 * @method InstagramAPI_Response_Model_User[] getBlockedList()
 * @method mixed getPageSize()
 * @method bool isBlockedList()
 * @method bool isPageSize()
 * @method setBlockedList(InstagramAPI_Response_Model_User[] $value)
 * @method setPageSize(mixed $value)
 */
class InstagramAPI_Response_BlockedListResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_User[]
     */
    public $blocked_list;
    public $page_size;

}
