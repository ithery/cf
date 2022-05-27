<?php

class CNotification_Message_Database extends CNotification_MessageAbstract {
    protected $type;

    public function send() {
        $model = $this->getOption('recipient');

        $data = $this->getOption('data');
        $message = $this->getOption('message');
        $title = $this->getOption('title');
        $data = $this->getOption('data');
        if (!($model instanceof CModel)) {
            throw new Exception('recipient for database channel must instance of CModel');
        }
        if ($data !== null && !is_array($data)) {
            throw new Exception('data for database channel must be an array');
        }
        if ($title !== null && strlen($title) > 1000) {
            throw new Exception('titile for database channel must be max 1000 chars');
        }
        if ($model instanceof CModel) {
            /** @var CModel $model */
            $notificationModelClass = CF::config('notification.database.model', CModel_Notification_NotificationModel::class);

            $notificationModel = new $notificationModelClass();
            $notificationModel->org_id = $this->getOption('orgId');
            $notificationModel->notifiable_id = $model->getKey();
            $notificationModel->notifiable_type = get_class($model);
            $notificationModel->type = $this->type;
            $notificationModel->data = $data;
            $notificationModel->title = $title;
            $notificationModel->message = $message;
            $notificationModel->createdby = $this->getOption('createdby', c::base()->username());
            $notificationModel->updatedby = $this->getOption('updatedby', c::base()->username());
            $notificationModel->save();
        }
    }

    public function setType($type) {
        $this->type = $type;

        return $this;
    }
}
