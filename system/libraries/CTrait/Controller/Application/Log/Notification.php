<?php

trait CTrait_Controller_Application_Log_Notification {
    public function notification() {
        if (!isset($this->logNotificationModel)) {
            $this->logNotificationModel = CNotification_Model_LogNotification::class;
        }
        $app = c::app();

        $table = $app->addTable();
        $table->setDataFromModel($this->logNotificationModel, function ($query) {
            $query->orderBy('log_notification_id', 'desc');
        });
        $table->setAjax();
        $table->addColumn('log_notification_id')
            ->setLabel('ID')
            ->setCallback(function ($row, $val) {
                $action = new CElement_Element_A();
                $action->add($val)
                    ->setHref($this->controllerUrl() . 'notificationDetail/' . $val);

                return $action;
            });
        $table->addColumn('vendor')->setLabel('Vendor');
        $table->addColumn('channel')->setLabel('Channel');
        $table->addColumn('message_class')->setLabel('Message Class');
        $table->addColumn('error')->setLabel('Status')->setCallback(function ($row, $value) {
            if ($value == null) {
                return 'SUCCESS';
            }

            return 'ERROR';
        });
        $table->addColumn('recipient')->setLabel('Recipient');
        $table->addColumn('createdby')->setLabel('Created By');
        $table->addColumn('created')->setLabel('Created');

        return $app;
    }

    public function notificationDetail($logNotificationId) {
        $app = c::app();
        if (!isset($this->logNotificationModel)) {
            $this->logNotificationModel = CNotification_Model_LogNotification::class;
        }

        $logNotificationModel = $this->logNotificationModel;
        $logNotificationModel = $logNotificationModel::findOrFail($logNotificationId);
        $title = 'Detail Notification';
        $app->setTitle($title);
        $app->addBreadcrumb('Log', static::controllerUrl() . '?tab=notification');
        $app->title($logNotificationModel->description);

        $form = $app->addForm();
        $divRow = $form->addDiv()->addClass('row');

        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('ID')
            ->addDiv()
            ->add($logNotificationModel->log_notification_id);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Channel')
            ->addDiv()
            ->add($logNotificationModel->channel);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Vendor')
            ->addDiv()
            ->add($logNotificationModel->vendor);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Recipient')
            ->addDiv()
            ->add($logNotificationModel->recipient);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Session Id')
            ->addDiv()
            ->add($logNotificationModel->session_id);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Ref ID')
            ->addDiv()
            ->add($logNotificationModel->ref_id);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Ref Type')
            ->addDiv()
            ->add($logNotificationModel->ref_type);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Message Class')
            ->addDiv()
            ->add($logNotificationModel->message_class);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Member ID')
            ->addDiv()
            ->add($logNotificationModel->member_id);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Member Type')
            ->addDiv()
            ->add($logNotificationModel->member_type);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Is Read')
            ->addDiv()
            ->add($logNotificationModel->is_read ? 'YES' : 'NO');
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Subject')
            ->addDiv()
            ->add($logNotificationModel->subject);

        $widget = $app->addWidget();
        $widget->setTitle('Message');
        $widget->setNoPadding();
        $widget->addClass('mb-3');
        $widget->addIframe()->setSrc($this->controllerUrl() . 'iframe/' . $logNotificationId)
            ->customCss('width', '100%')
            ->customCss('border', 'none')
            ->customCss('height', '500px');

        if ($logNotificationModel->error) {
            $widget = $app->addWidget();
            $widget->setTitle('Error');
            $widget->addPre()->add($logNotificationModel->error);
        }

        return $app;
    }

    public function iframe($logNotificationId) {
        $app = c::app();
        if (!isset($this->logNotificationModel)) {
            $this->logNotificationModel = CNotification_Model_LogNotification::class;
        }

        $logNotificationModel = $this->logNotificationModel;
        $logNotificationModel = $logNotificationModel::findOrFail($logNotificationId);

        $message = $logNotificationModel->message;
        if (cstr::tolower($logNotificationModel->channel) == 'whatsapp') {
            $style = <<<HTML
                <style>
                body {
                    font-family: Segoe UI,Helvetica Neue,Helvetica,Lucida Grande,Arial,Ubuntu,Cantarell,Fira Sans,sans-serif;
                }
                </style>
            HTML;

            $message = nl2br($message);
            $message = $style . $message;
        }

        return c::response($message);
    }
}
