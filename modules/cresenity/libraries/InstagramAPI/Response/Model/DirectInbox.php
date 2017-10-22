<?php

/**
 * @method mixed getHasOlder()
 * @method mixed getOldestCursor()
 * @method InstagramAPI_Response_Model_DirectThread[] getThreads()
 * @method mixed getUnseenCount()
 * @method mixed getUnseenCountTs()
 * @method bool isHasOlder()
 * @method bool isOldestCursor()
 * @method bool isThreads()
 * @method bool isUnseenCount()
 * @method bool isUnseenCountTs()
 * @method setHasOlder(mixed $value)
 * @method setOldestCursor(mixed $value)
 * @method setThreads(InstagramAPI_Response_Model_DirectThread[] $value)
 * @method setUnseenCount(mixed $value)
 * @method setUnseenCountTs(mixed $value)
 */
class InstagramAPI_Response_Model_DirectInbox extends InstagramAPI_AutoPropertyHandler {

    public $unseen_count;
    public $has_older;
    public $oldest_cursor;
    public $unseen_count_ts; // is a timestamp
    /**
     * @var InstagramAPI_Response_Model_DirectThread[]
     */
    public $threads;

}
