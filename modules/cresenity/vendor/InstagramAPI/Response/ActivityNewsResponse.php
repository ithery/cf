<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * ActivityNewsResponse.
 *
 * @method mixed getAdsManager()
 * @method Model\Aymf getAymf()
 * @method mixed getContinuation()
 * @method mixed getContinuationToken()
 * @method Model\Counts getCounts()
 * @method Model\Story[] getFriendRequestStories()
 * @method mixed getMessage()
 * @method Model\Story[] getNewStories()
 * @method Model\Story[] getOldStories()
 * @method string getStatus()
 * @method Model\Subscription getSubscription()
 * @method Model\ZMessage[] getZMessages()
 * @method bool isAdsManager()
 * @method bool isAymf()
 * @method bool isContinuation()
 * @method bool isContinuationToken()
 * @method bool isCounts()
 * @method bool isFriendRequestStories()
 * @method bool isMessage()
 * @method bool isNewStories()
 * @method bool isOldStories()
 * @method bool isStatus()
 * @method bool isSubscription()
 * @method bool isZMessages()
 * @method $this setAdsManager(mixed $value)
 * @method $this setAymf(Model\Aymf $value)
 * @method $this setContinuation(mixed $value)
 * @method $this setContinuationToken(mixed $value)
 * @method $this setCounts(Model\Counts $value)
 * @method $this setFriendRequestStories(Model\Story[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setNewStories(Model\Story[] $value)
 * @method $this setOldStories(Model\Story[] $value)
 * @method $this setStatus(string $value)
 * @method $this setSubscription(Model\Subscription $value)
 * @method $this setZMessages(Model\ZMessage[] $value)
 * @method $this unsetAdsManager()
 * @method $this unsetAymf()
 * @method $this unsetContinuation()
 * @method $this unsetContinuationToken()
 * @method $this unsetCounts()
 * @method $this unsetFriendRequestStories()
 * @method $this unsetMessage()
 * @method $this unsetNewStories()
 * @method $this unsetOldStories()
 * @method $this unsetStatus()
 * @method $this unsetSubscription()
 * @method $this unsetZMessages()
 */
class ActivityNewsResponse extends Response
{
    public static $JSON_PROPERTY_MAP = [
        'new_stories'            => 'Model\Story[]',
        'old_stories'            => 'Model\Story[]',
        'continuation'           => '',
        'friend_request_stories' => 'Model\Story[]',
        'counts'                 => 'Model\Counts',
        'subscription'           => 'Model\Subscription',
        'continuation_token'     => '',
        'ads_manager'            => '',
        'aymf'                   => 'Model\Aymf',
    ];
}
