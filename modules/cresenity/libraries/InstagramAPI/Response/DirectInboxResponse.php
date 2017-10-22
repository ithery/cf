<?php

/**
 * @method InstagramAPI_Response_Model_DirectInbox getInbox()
 * @method InstagramAPI_Response_Model_Megaphone getMegaphone()
 * @method mixed getPendingRequestsTotal()
 * @method InstagramAPI_Response_Model_User[] getPendingRequestsUsers()
 * @method string getSeqId()
 * @method bool isInbox()
 * @method bool isMegaphone()
 * @method bool isPendingRequestsTotal()
 * @method bool isPendingRequestsUsers()
 * @method bool isSeqId()
 * @method setInbox(InstagramAPI_Response_Model_DirectInbox $value)
 * @method setMegaphone(InstagramAPI_Response_Model_Megaphone $value)
 * @method setPendingRequestsTotal(mixed $value)
 * @method setPendingRequestsUsers(InstagramAPI_Response_Model_User[] $value)
 * @method setSeqId(string $value)
 */
class InstagramAPI_Response_DirectInboxResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $pending_requests_total;

    /**
     * @var string
     */
    public $seq_id;

    /**
     * @var InstagramAPI_Response_Model_User[]
     */
    public $pending_requests_users;

    /**
     * @var InstagramAPI_Response_Model_DirectInbox
     */
    public $inbox;

    /**
     * @var InstagramAPI_Response_Model_Megaphone
     */
    public $megaphone;

}
