<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * FriendshipsShowResponse.
 *
 * @method bool getBlocking()
 * @method bool getFollowedBy()
 * @method bool getFollowing()
 * @method bool getIncomingRequest()
 * @method bool getIsBestie()
 * @method bool getIsBlockingReel()
 * @method bool getIsMutingReel()
 * @method bool getIsPrivate()
 * @method mixed getMessage()
 * @method bool getOutgoingRequest()
 * @method string getStatus()
 * @method Model\ZMessage[] getZMessages()
 * @method bool isBlocking()
 * @method bool isFollowedBy()
 * @method bool isFollowing()
 * @method bool isIncomingRequest()
 * @method bool isIsBestie()
 * @method bool isIsBlockingReel()
 * @method bool isIsMutingReel()
 * @method bool isIsPrivate()
 * @method bool isMessage()
 * @method bool isOutgoingRequest()
 * @method bool isStatus()
 * @method bool isZMessages()
 * @method $this setBlocking(bool $value)
 * @method $this setFollowedBy(bool $value)
 * @method $this setFollowing(bool $value)
 * @method $this setIncomingRequest(bool $value)
 * @method $this setIsBestie(bool $value)
 * @method $this setIsBlockingReel(bool $value)
 * @method $this setIsMutingReel(bool $value)
 * @method $this setIsPrivate(bool $value)
 * @method $this setMessage(mixed $value)
 * @method $this setOutgoingRequest(bool $value)
 * @method $this setStatus(string $value)
 * @method $this setZMessages(Model\ZMessage[] $value)
 * @method $this unsetBlocking()
 * @method $this unsetFollowedBy()
 * @method $this unsetFollowing()
 * @method $this unsetIncomingRequest()
 * @method $this unsetIsBestie()
 * @method $this unsetIsBlockingReel()
 * @method $this unsetIsMutingReel()
 * @method $this unsetIsPrivate()
 * @method $this unsetMessage()
 * @method $this unsetOutgoingRequest()
 * @method $this unsetStatus()
 * @method $this unsetZMessages()
 */
class FriendshipsShowResponse extends Response
{
    public static $JSON_PROPERTY_MAP = [
        Model\FriendshipStatus::class, // Import property map.
    ];
}
