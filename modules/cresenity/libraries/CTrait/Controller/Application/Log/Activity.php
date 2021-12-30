<?php

trait CTrait_Controller_Application_Log_Activity {
    public function activity() {
        $app = c::app();
        $logActivityClass = '';

        try {
            $logActivityClass = $this->logActivityModel;
        } catch (Exception $ex) {
            cmsg::add('error', $ex->getMessage());

            return $app;
        }
        $logActivityModel = new $logActivityClass();
        if (!$logActivityModel instanceof CModel) {
            cmsg::add('error', '"logActivityModel" must be instance of CModel');

            return $app;
        }

        $logActivityModel = $logActivityModel->orderBy('activity_date', 'desc');
        $table = $app->addTable();
        $table->setDataFromModel($logActivityModel);
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
                    ->setUrl($this->controllerUrl() . "activityDetail/${val}");

                return $action;
            });
        $table->addColumn('remote_addr')->setLabel('Remote Address');
        $table->addColumn('platform')->setLabel('Platform');
        $table->addColumn('browser')->setLabel('Browser')->setCallback(function ($row, $val) {
            $version = carr::get($row, 'browser_version');

            return "${val} (${version})";
        });
        $table->addColumn('uri')->setLabel('URI');
        $table->addColumn('activity_date')->setLabel('Time');

        return $app;
    }

    public function activityDetail($logActivityId) {
        $app = c::app();

        try {
            $logActivityClass = $this->logActivityModel;
        } catch (Exception $ex) {
            cmsg::add('error', $ex->getMessage());

            return $app;
        }

        $logActivityModel = new $logActivityClass();
        if (!$logActivityModel instanceof CModel) {
            cmsg::add('error', '"logActivityModel" must be instance of CModel');

            return $app;
        }

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
