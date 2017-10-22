<?php

/**
 * @method InstagramAPI_Response_Model_ActionBadge getActionBadge()
 * @method mixed getCanonical()
 * @method mixed getHasNewer()
 * @method mixed getHasOlder()
 * @method InstagramAPI_Response_Model_User getInviter()
 * @method mixed getIsPin()
 * @method mixed getIsSpam()
 * @method InstagramAPI_Response_Model_DirectThreadItem[] getItems()
 * @method mixed getLastActivityAt()
 * @method mixed getLastActivityAtSecs()
 * @method InstagramAPI_Response_Model_PermanentItem getLastPermanentItem()
 * @method InstagramAPI_Response_Model_DirectThreadLastSeenAt[] getLastSeenAt()
 * @method InstagramAPI_Response_Model_User[] getLeftUsers()
 * @method mixed getMuted()
 * @method mixed getNamed()
 * @method mixed getNewestCursor()
 * @method mixed getOldestCursor()
 * @method mixed getPending()
 * @method string getThreadId()
 * @method mixed getThreadTitle()
 * @method mixed getThreadType()
 * @method mixed getUnseenCount()
 * @method InstagramAPI_Response_Model_User[] getUsers()
 * @method string getViewerId()
 * @method bool isActionBadge()
 * @method bool isCanonical()
 * @method bool isHasNewer()
 * @method bool isHasOlder()
 * @method bool isInviter()
 * @method bool isIsPin()
 * @method bool isIsSpam()
 * @method bool isItems()
 * @method bool isLastActivityAt()
 * @method bool isLastActivityAtSecs()
 * @method bool isLastPermanentItem()
 * @method bool isLastSeenAt()
 * @method bool isLeftUsers()
 * @method bool isMuted()
 * @method bool isNamed()
 * @method bool isNewestCursor()
 * @method bool isOldestCursor()
 * @method bool isPending()
 * @method bool isThreadId()
 * @method bool isThreadTitle()
 * @method bool isThreadType()
 * @method bool isUnseenCount()
 * @method bool isUsers()
 * @method bool isViewerId()
 * @method setActionBadge(InstagramAPI_Response_Model_ActionBadge $value)
 * @method setCanonical(mixed $value)
 * @method setHasNewer(mixed $value)
 * @method setHasOlder(mixed $value)
 * @method setInviter(InstagramAPI_Response_Model_User $value)
 * @method setIsPin(mixed $value)
 * @method setIsSpam(mixed $value)
 * @method setItems(InstagramAPI_Response_Model_DirectThreadItem[] $value)
 * @method setLastActivityAt(mixed $value)
 * @method setLastActivityAtSecs(mixed $value)
 * @method setLastPermanentItem(InstagramAPI_Response_Model_PermanentItem $value)
 * @method setLastSeenAt(InstagramAPI_Response_Model_DirectThreadLastSeenAt[] $value)
 * @method setLeftUsers(InstagramAPI_Response_Model_User[] $value)
 * @method setMuted(mixed $value)
 * @method setNamed(mixed $value)
 * @method setNewestCursor(mixed $value)
 * @method setOldestCursor(mixed $value)
 * @method setPending(mixed $value)
 * @method setThreadId(string $value)
 * @method setThreadTitle(mixed $value)
 * @method setThreadType(mixed $value)
 * @method setUnseenCount(mixed $value)
 * @method setUsers(InstagramAPI_Response_Model_User[] $value)
 * @method setViewerId(string $value)
 */
class InstagramAPI_Response_Model_DirectThread extends InstagramAPI_AutoPropertyHandler {

    // NOTE: We must use full paths to all model objects in THIS class, because
    // "DirectVisualThreadResponse" re-uses this object and JSONMapper won't be
    // able to find these sub-objects if the paths aren't absolute!

    public $named;

    /**
     * @var InstagramAPI_Response_Model_User[]
     */
    public $users;
    public $has_newer;

    /**
     * @var string
     */
    public $viewer_id;

    /**
     * @var string
     */
    public $thread_id;
    public $last_activity_at;
    public $newest_cursor;
    public $is_spam;
    public $has_older;
    public $oldest_cursor;

    /**
     * @var InstagramAPI_Response_Model_User[]
     */
    public $left_users;
    public $muted;

    /**
     * @var InstagramAPI_Response_Model_DirectThreadItem[]
     */
    public $items;
    public $thread_type;
    public $thread_title;
    public $canonical;

    /**
     * @var InstagramAPI_Response_Model_User
     */
    public $inviter;
    public $pending;

    /**
     * @var InstagramAPI_Response_Model_DirectThreadLastSeenAt[]
     */
    public $last_seen_at;
    public $unseen_count;

    /**
     * @var InstagramAPI_Response_Model_ActionBadge
     */
    public $action_badge;
    public $last_activity_at_secs;

    /**
     * @var InstagramAPI_Response_Model_PermanentItem
     */
    public $last_permanent_item;
    public $is_pin;

}
