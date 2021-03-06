<?php

namespace InstagramAPI\Response;

use InstagramAPI\Response;

/**
 * ConfigureResponse.
 *
 * @method string getClientSidecarId()
 * @method Model\Item getMedia()
 * @method mixed getMessage()
 * @method Model\DirectMessageMetadata[] getMessageMetadata()
 * @method string getStatus()
 * @method string getUploadId()
 * @method Model\ZMessage[] getZMessages()
 * @method bool isClientSidecarId()
 * @method bool isMedia()
 * @method bool isMessage()
 * @method bool isMessageMetadata()
 * @method bool isStatus()
 * @method bool isUploadId()
 * @method bool isZMessages()
 * @method $this setClientSidecarId(string $value)
 * @method $this setMedia(Model\Item $value)
 * @method $this setMessage(mixed $value)
 * @method $this setMessageMetadata(Model\DirectMessageMetadata[] $value)
 * @method $this setStatus(string $value)
 * @method $this setUploadId(string $value)
 * @method $this setZMessages(Model\ZMessage[] $value)
 * @method $this unsetClientSidecarId()
 * @method $this unsetMedia()
 * @method $this unsetMessage()
 * @method $this unsetMessageMetadata()
 * @method $this unsetStatus()
 * @method $this unsetUploadId()
 * @method $this unsetZMessages()
 */
class ConfigureResponse extends Response
{
    public static $JSON_PROPERTY_MAP = [
        'upload_id'         => 'string',
        'media'             => 'Model\Item',
        'client_sidecar_id' => 'string',
        'message_metadata'  => 'Model\DirectMessageMetadata[]',
    ];
}
