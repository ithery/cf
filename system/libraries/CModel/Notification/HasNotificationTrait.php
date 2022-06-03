<?php
trait CModel_Notification_HasNotificationTrait {
    /**
     * Get the entity's notifications.
     *
     * @return \CModel_RelationMorphMany
     */
    public function notification() {
        $notificationModelClass = CF::config('notification.database.model', CModel_Notification_NotificationModel::class);

        return $this->morphMany($notificationModelClass, 'notifiable')->latest();
    }

    /**
     * Get the entity's read notifications.
     *
     * @return \CDatabase_Query_Builder
     */
    public function readNotification() {
        return $this->notification()->read();
    }

    /**
     * Get the entity's unread notifications.
     *
     * @return \CDatabase_Query_Builder
     */
    public function unreadNotification() {
        return $this->notification()->unread();
    }
}
