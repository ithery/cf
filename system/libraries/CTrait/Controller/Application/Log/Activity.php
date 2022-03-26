<?php

trait CTrait_Controller_Application_Log_Activity {
    public function activity() {
        if (!isset($this->logActivityModel)) {
            $this->logActivityModel = CApp_Model_LogActivity::class;
        }
        $app = c::app();

        $table = $app->addTable();
        $table->setDataFromModel($this->logActivityModel, function ($query) {
            $query->orderBy('log_activity_id', 'desc');
        });
        $table->setAjax();
        $table->addColumn('log_activity_id')
            ->setLabel('ID')
            ->setCallback(function ($row, $val) {
                $action = new CElement_Element_A();
                $action->add($val)
                    ->setHref('javascript:;')
                    ->onClickListener()
                    ->addDialogHandler()
                    ->setTitle('Activity Detail')
                    ->setUrl($this->controllerUrl() . 'activityDetail/' . $val);

                return $action;
            });
        $table->addColumn('description')->setLabel('Description');
        $table->addColumn('createdby')->setLabel('By');
        $table->addColumn('platform')->setLabel('Platform');
        $table->addColumn('browser')->setLabel('Browser')->setCallback(function ($row, $val) {
            $version = carr::get($row, 'browser_version');

            return $val . ' (' . $version . ')';
        });
        $table->addColumn('uri')->setLabel('URI');
        $table->addColumn('activity_date')->setLabel('Time');

        return $app;
    }

    public function activityDetail($logActivityId) {
        $app = c::app();
        if (!isset($this->logActivityModel)) {
            $this->logActivityModel = CApp_Model_LogActivity::class;
        }
        $logActivityModel = $this->logActivityModel;
        $logActivityModel = $logActivityModel::find($logActivityId);
        if (!$logActivityModel) {
            cmsg::add('error', 'Log Activity not found');

            return $app;
        }

        $form = $app->addForm();
        $form->addField()
            ->setLabel('ID')
            ->addDiv()
            ->add($logActivityModel->log_activity_id);
        $form->addField()
            ->setLabel('Description')
            ->addDiv()
            ->add($logActivityModel->description);
        $form->addField()
            ->setLabel('User Id')
            ->addDiv()
            ->add($logActivityModel->user_id);
        $form->addField()
            ->setLabel('App Id')
            ->addDiv()
            ->add($logActivityModel->app_id);
        $form->addField()
            ->setLabel('Session Id')
            ->addDiv()
            ->add($logActivityModel->session_id);
        $form->addField()
            ->setLabel('Remote Address')
            ->addDiv()
            ->add($logActivityModel->remote_addr);
        $form->addField()
            ->setLabel('User Agent')
            ->addDiv()
            ->add($logActivityModel->user_agent);
        $form->addField()
            ->setLabel('Platform')
            ->addDiv()
            ->add($logActivityModel->platform);
        $form->addField()
            ->setLabel('Browser')
            ->addDiv()
            ->add($logActivityModel->browser . ' (' . $logActivityModel->browser_version . ')');
        $form->addField()
            ->setLabel('Uri')
            ->addDiv()
            ->add($logActivityModel->uri);
        $form->addField()
            ->setLabel('Routed Uri')
            ->addDiv()
            ->add($logActivityModel->routed_uri);
        $form->addField()
            ->setLabel('Controller')
            ->addDiv()
            ->add($logActivityModel->controller);
        $form->addField()
            ->setLabel('Method')
            ->addDiv()
            ->add($logActivityModel->method);
        $form->addField()
            ->setLabel('Query String')
            ->addDiv()
            ->add($logActivityModel->query_string);
        $form->addField()
            ->setLabel('Nav')
            ->addDiv()
            ->add($logActivityModel->nav);
        $form->addField()
            ->setLabel('Nav Label')
            ->addDiv()
            ->add($logActivityModel->nav_label);
        $form->addField()
            ->setLabel('Data')
            ->addDiv()
            ->add($logActivityModel->data);

        return $app;
    }
}
