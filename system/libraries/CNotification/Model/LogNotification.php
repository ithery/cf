<?php

/**
 * @property-read int                         $log_notification_id
 * @property      null|int                    $org_id
 * @property      null|int                    $member_id
 * @property      null|string                 $member_type
 * @property      null|string                 $message_class
 * @property      null|string                 $vendor
 * @property      null|string                 $channel
 * @property      null|string                 $notification_status
 * @property      int                         $is_read
 * @property      null|string                 $recipient
 * @property      null|string                 $subject
 * @property      null|string                 $message
 * @property      null|string                 $options
 * @property      null|string                 $error
 * @property      null|string                 $vendor_response
 * @property      null|int                    $ref_id
 * @property      null|string                 $ref_type
 * @property      null|string                 $createdip
 * @property      null|string                 $updatedip
 * @property      null|CCarbon|\Carbon\Carbon $deleted
 * @property      null|string                 $deletedby
 */
class CNotification_Model_LogNotification extends CModel {
    use CNotification_Trait_LogNotificationModelTrait;
}
