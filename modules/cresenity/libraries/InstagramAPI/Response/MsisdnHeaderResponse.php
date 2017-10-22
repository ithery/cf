<?php


/**
 * @method string getPhoneNumber()
 * @method string getUrl()
 * @method bool isPhoneNumber()
 * @method bool isUrl()
 * @method setPhoneNumber(string $value)
 * @method setUrl(string $value)
 */
class InstagramAPI_Response_MsisdnHeaderResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface
{
    use InstagramAPI_ResponseTrait;

    /** @var string */
    public $phone_number;

    /** @var string */
    public $url;
}
