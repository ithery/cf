<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * BroadcastHeartbeatAndViewerCountResponse.
 *
 * @method string getBroadcastStatus()
 * @method mixed getCobroadcasterIds()
 * @method int getIsTopLiveEligible()
 * @method mixed getMessage()
 * @method int getOffsetToVideoStart()
 * @method string getStatus()
 * @method int getTotalUniqueViewerCount()
 * @method int getViewerCount()
 * @method Model\ZMessage[] getZMessages()
 * @method bool isBroadcastStatus()
 * @method bool isCobroadcasterIds()
 * @method bool isIsTopLiveEligible()
 * @method bool isMessage()
 * @method bool isOffsetToVideoStart()
 * @method bool isStatus()
 * @method bool isTotalUniqueViewerCount()
 * @method bool isViewerCount()
 * @method bool isZMessages()
 * @method $this setBroadcastStatus(string $value)
 * @method $this setCobroadcasterIds(mixed $value)
 * @method $this setIsTopLiveEligible(int $value)
 * @method $this setMessage(mixed $value)
 * @method $this setOffsetToVideoStart(int $value)
 * @method $this setStatus(string $value)
 * @method $this setTotalUniqueViewerCount(int $value)
 * @method $this setViewerCount(int $value)
 * @method $this setZMessages(Model\ZMessage[] $value)
 * @method $this unsetBroadcastStatus()
 * @method $this unsetCobroadcasterIds()
 * @method $this unsetIsTopLiveEligible()
 * @method $this unsetMessage()
 * @method $this unsetOffsetToVideoStart()
 * @method $this unsetStatus()
 * @method $this unsetTotalUniqueViewerCount()
 * @method $this unsetViewerCount()
 * @method $this unsetZMessages()
 */
class BroadcastHeartbeatAndViewerCountResponse extends Response
{
    public static $JSON_PROPERTY_MAP = [
        'broadcast_status'          => 'string',
        'viewer_count'              => 'int',
        'offset_to_video_start'     => 'int',
        'total_unique_viewer_count' => 'int',
        'is_top_live_eligible'      => 'int',
        'cobroadcaster_ids'         => '',
    ];
}
