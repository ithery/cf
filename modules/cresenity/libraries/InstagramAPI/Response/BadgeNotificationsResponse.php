<?php

/**
 * @method mixed getBadgePayload()
 * @method bool isBadgePayload()
 * @method setBadgePayload(mixed $value)
 */
class InstagramAPI_Response_BadgeNotificationsResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $badge_payload; // Only exists if you have notifications, contains data keyed by userId.

}
