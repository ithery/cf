<?php

/**
 * @method mixed getBody()
 * @method mixed getIsEmailLegit()
 * @method mixed getTitle()
 * @method bool isBody()
 * @method bool isIsEmailLegit()
 * @method bool isTitle()
 * @method setBody(mixed $value)
 * @method setIsEmailLegit(mixed $value)
 * @method setTitle(mixed $value)
 */
class InstagramAPI_Response_SendConfirmEmailResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $title;
    public $is_email_legit;
    public $body;

}
