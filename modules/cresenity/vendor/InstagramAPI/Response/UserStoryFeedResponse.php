<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * UserStoryFeedResponse.
 *
 * @method Model\Broadcast getBroadcast()
 * @method mixed getMessage()
 * @method Model\PostLiveItem getPostLiveItem()
 * @method Model\Reel getReel()
 * @method string getStatus()
 * @method Model\ZMessage[] getZMessages()
 * @method bool isBroadcast()
 * @method bool isMessage()
 * @method bool isPostLiveItem()
 * @method bool isReel()
 * @method bool isStatus()
 * @method bool isZMessages()
 * @method $this setBroadcast(Model\Broadcast $value)
 * @method $this setMessage(mixed $value)
 * @method $this setPostLiveItem(Model\PostLiveItem $value)
 * @method $this setReel(Model\Reel $value)
 * @method $this setStatus(string $value)
 * @method $this setZMessages(Model\ZMessage[] $value)
 * @method $this unsetBroadcast()
 * @method $this unsetMessage()
 * @method $this unsetPostLiveItem()
 * @method $this unsetReel()
 * @method $this unsetStatus()
 * @method $this unsetZMessages()
 */
class UserStoryFeedResponse extends Response
{
    public static $JSON_PROPERTY_MAP = [
        'broadcast'      => 'Model\Broadcast',
        'reel'           => 'Model\Reel',
        'post_live_item' => 'Model\PostLiveItem',
    ];
}
