<?php

/**
 * @method mixed getAdsManager()
 * @method InstagramAPI_Response_Model_Aymf getAymf()
 * @method mixed getContinuation()
 * @method mixed getContinuationToken()
 * @method InstagramAPI_Response_Model_Counts getCounts()
 * @method InstagramAPI_Response_Model_Story[] getFriendRequestStories()
 * @method InstagramAPI_Response_Model_Story[] getNewStories()
 * @method InstagramAPI_Response_Model_Story[] getOldStories()
 * @method InstagramAPI_Response_Model_Subscription getSubscription()
 * @method bool isAdsManager()
 * @method bool isAymf()
 * @method bool isContinuation()
 * @method bool isContinuationToken()
 * @method bool isCounts()
 * @method bool isFriendRequestStories()
 * @method bool isNewStories()
 * @method bool isOldStories()
 * @method bool isSubscription()
 * @method setAdsManager(mixed $value)
 * @method setAymf(InstagramAPI_Response_Model_Aymf $value)
 * @method setContinuation(mixed $value)
 * @method setContinuationToken(mixed $value)
 * @method setCounts(InstagramAPI_Response_Model_Counts $value)
 * @method setFriendRequestStories(InstagramAPI_Response_Model_Story[] $value)
 * @method setNewStories(InstagramAPI_Response_Model_Story[] $value)
 * @method setOldStories(InstagramAPI_Response_Model_Story[] $value)
 * @method setSubscription(InstagramAPI_Response_Model_Subscription $value)
 */
class InstagramAPI_Response_ActivityNewsResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    /**
     * @var InstagramAPI_Response_Model_Story[]
     */
    public $new_stories;

    /**
     * @var InstagramAPI_Response_Model_Story[]
     */
    public $old_stories;
    public $continuation;

    /**
     * @var InstagramAPI_Response_Model_Story[]
     */
    public $friend_request_stories;

    /**
     * @var InstagramAPI_Response_Model_Counts
     */
    public $counts;

    /**
     * @var InstagramAPI_Response_Model_Subscription
     */
    public $subscription;
    public $continuation_token;
    public $ads_manager;

    /**
     * @var InstagramAPI_Response_Model_Aymf
     */
    public $aymf;

}
