<?php

class CModel_Notification_NotificationCollection extends CModel_Collection {
    /**
     * Mark all notifications as read.
     *
     * @return void
     */
    public function markAsRead() {
        $this->each->markAsRead();
    }

    /**
     * Mark all notifications as unread.
     *
     * @return void
     */
    public function markAsUnread() {
        $this->each->markAsUnread();
    }
}
