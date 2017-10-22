<?php

/**
 * @method InstagramAPI_Response_Model_Suggestion[] getItems()
 * @method bool isItems()
 * @method setItems(InstagramAPI_Response_Model_Suggestion[] $value)
 */
class InstagramAPI_Response_LinkAddressBookResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_Suggestion[]
     */
    public $items;

}
