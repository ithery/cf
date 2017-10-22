<?php



/**
 * @method mixed getAccountCreated()
 * @method InstagramAPI_Response_Model_User getCreatedUser()
 * @method bool isAccountCreated()
 * @method bool isCreatedUser()
 * @method setAccountCreated(mixed $value)
 * @method setCreatedUser(InstagramAPI_Response_Model_User $value)
 */
class InstagramAPI_Response_AccountCreateResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface
{
    use InstagramAPI_ResponseTrait;

    public $account_created;
    /**
     * @var InstagramAPI_Response_Model_User
     */
    public $created_user;
}
