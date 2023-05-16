<?php
/**
 * @property-read int                         $notification_id
 * @property      null|int                    $org_id
 * @property      string                      $notifiable_type
 * @property      int                         $notifiable_id
 * @property      null|string                 $type
 * @property      null|array                  $data
 * @property      null|string                 $title
 * @property      null|string                 $message
 * @property      null|CCarbon|\Carbon\Carbon $read_at
 */
class CModel_Notification_NotificationModel extends CModel {
    use CModel_Notification_NotificationTrait;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification';

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = [
        'notification_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];
}
