<?php

/**
 * @method mixed getExpirationInterval()
 * @method mixed getRecentRecipients()
 * @method bool isExpirationInterval()
 * @method bool isRecentRecipients()
 * @method setExpirationInterval(mixed $value)
 * @method setRecentRecipients(mixed $value)
 */
class InstagramAPI_Response_DirectRecentRecipientsResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $expiration_interval;
    public $recent_recipients;

}
