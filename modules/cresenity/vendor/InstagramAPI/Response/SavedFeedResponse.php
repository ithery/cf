<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * SavedFeedResponse.
 *
 * @method mixed getAutoLoadMoreEnabled()
 * @method Model\SavedFeedItem[] getItems()
 * @method mixed getMessage()
 * @method mixed getMoreAvailable()
 * @method string getNextMaxId()
 * @method int getNumResults()
 * @method string getStatus()
 * @method Model\ZMessage[] getZMessages()
 * @method bool isAutoLoadMoreEnabled()
 * @method bool isItems()
 * @method bool isMessage()
 * @method bool isMoreAvailable()
 * @method bool isNextMaxId()
 * @method bool isNumResults()
 * @method bool isStatus()
 * @method bool isZMessages()
 * @method $this setAutoLoadMoreEnabled(mixed $value)
 * @method $this setItems(Model\SavedFeedItem[] $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMoreAvailable(mixed $value)
 * @method $this setNextMaxId(string $value)
 * @method $this setNumResults(int $value)
 * @method $this setStatus(string $value)
 * @method $this setZMessages(Model\ZMessage[] $value)
 * @method $this unsetAutoLoadMoreEnabled()
 * @method $this unsetItems()
 * @method $this unsetMessage()
 * @method $this unsetMoreAvailable()
 * @method $this unsetNextMaxId()
 * @method $this unsetNumResults()
 * @method $this unsetStatus()
 * @method $this unsetZMessages()
 */
class SavedFeedResponse extends Response
{
    public static $JSON_PROPERTY_MAP = [
        'items'                  => 'Model\SavedFeedItem[]',
        'more_available'         => '',
        'next_max_id'            => 'string',
        'auto_load_more_enabled' => '',
        'num_results'            => 'int',
    ];
}
