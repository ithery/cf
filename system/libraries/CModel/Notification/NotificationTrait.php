<?php
trait CModel_Notification_NotificationTrait {
    /**
     * Get the notifiable entity that the notification belongs to.
     *
     * @return \CModel_Relation_MorphTo
     */
    public function notifiable() {
        return $this->morphTo();
    }

    /**
     * Get the notifiable entity that the notification belongs to.
     *
     * @return \CModel_Relation_MorphTo
     */
    public function ref() {
        return $this->morphTo();
    }

    /**
     * Mark the notification as read.
     *
     * @return void
     */
    public function markAsRead() {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

    /**
     * Mark the notification as unread.
     *
     * @return void
     */
    public function markAsUnread() {
        if (!is_null($this->read_at)) {
            $this->forceFill(['read_at' => null])->save();
        }
    }

    /**
     * Determine if a notification has been read.
     *
     * @return bool
     */
    public function read() {
        return $this->read_at !== null;
    }

    /**
     * Determine if a notification has not been read.
     *
     * @return bool
     */
    public function unread() {
        return $this->read_at === null;
    }

    /**
     * Scope a query to only include read notifications.
     *
     * @param \CModel_Query $query
     *
     * @return \CModel_Query
     */
    public function scopeRead(CModel_Query $query) {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope a query to only include unread notifications.
     *
     * @param \CModel_Query $query
     *
     * @return \CModel_Query
     */
    public function scopeUnread(CModel_Query $query) {
        return $query->whereNull('read_at');
    }

    /**
     * Create a new database notification collection instance.
     *
     * @param array $models
     *
     * @return \CModel_Notification_NotificationCollection
     */
    public function newCollection(array $models = []) {
        return new CModel_Notification_NotificationCollection($models);
    }
}
