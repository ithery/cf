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
                    ->setHref($this->controllerUrl() . 'activityDetail/' . $val);

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
        $logActivityModel = $logActivityModel::findOrFail($logActivityId);
        $title = 'Activity';
        if (method_exists($this, 'getTitle')) {
            $title = $this->getTitle();
        }
        $app->addBreadcrumb($title, static::controllerUrl() . '?tab=activity');
        $app->title($logActivityModel->description);

        $form = $app->addForm();
        $divRow = $form->addDiv()->addClass('row');

        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('ID')
            ->addDiv()
            ->add($logActivityModel->log_activity_id);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Description')
            ->addDiv()
            ->add($logActivityModel->description);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('User Id')
            ->addDiv()
            ->add($logActivityModel->user_id);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('App Id')
            ->addDiv()
            ->add($logActivityModel->app_id);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Session Id')
            ->addDiv()
            ->add($logActivityModel->session_id);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Remote Address')
            ->addDiv()
            ->add($logActivityModel->remote_addr);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('User Agent')
            ->addDiv()
            ->add($logActivityModel->user_agent);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Platform')
            ->addDiv()
            ->add($logActivityModel->platform);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Browser')
            ->addDiv()
            ->add($logActivityModel->browser . ' (' . $logActivityModel->browser_version . ')');
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Uri')
            ->addDiv()
            ->add($logActivityModel->uri);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Routed Uri')
            ->addDiv()
            ->add($logActivityModel->routed_uri);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Controller')
            ->addDiv()
            ->add($logActivityModel->controller);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Method')
            ->addDiv()
            ->add($logActivityModel->method);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Query String')
            ->addDiv()
            ->add($logActivityModel->query_string);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Nav')
            ->addDiv()
            ->add($logActivityModel->nav);
        $divRow->addDiv()->addClass('col-md-4')->addField()
            ->setLabel('Nav Label')
            ->addDiv()
            ->add($logActivityModel->nav_label);

        $originalData = $logActivityModel->data;
        $data = $originalData;
        if (!is_array($data)) {
            $data = json_decode($data, true);
        }
        if (!is_array($data)) {
            $divRow->addDiv()->addClass('col-md-4')->addField()
                ->setLabel('Data')
                ->addDiv()
                ->add($originalData);
        } else {
            //display the data
            $dataDiv = $divRow->addDiv()->addClass('col-md-12');
            foreach ($data as $record) {
                $table = carr::get($record, 'table');
                $key = carr::get($record, 'key');
                $type = carr::get($record, 'type');
                $beforeData = carr::get($record, 'before', []);
                $afterData = carr::get($record, 'after', []);
                $beforeKeys = c::collect($beforeData)->mapWithKeys(function ($before, $key) {
                    return [$key => $key];
                })->toArray();
                $afterKeys = c::collect($afterData)->mapWithKeys(function ($before, $key) {
                    return [$key => $key];
                })->toArray();
                $keys = array_merge($beforeKeys, $afterKeys);
                $changedData = c::collect($keys)->map(function ($value, $key) use ($beforeData, $afterData) {
                    $beforeValue = carr::get($beforeData, $key);
                    $afterValue = carr::get($afterData, $key);
                    $beforeValue = $this->transformToString($beforeValue);
                    $afterValue = $this->transformToString($afterValue);
                    $isChanged = $beforeValue != $afterValue;

                    return [
                        'key' => $key,
                        'before' => $beforeValue,
                        'after' => $afterValue,
                        'isChanged' => (bool) $isChanged,
                    ];
                })->toArray();
                $keyBadge = ' <span class="badge badge-success">' . c::e($key) . '</span>';
                $typeBadge = ' <span class="badge badge-info">' . c::e($type) . '</span>';
                $widgetData = $dataDiv->addWidget();
                $widgetData->setTitle($table . $keyBadge . $typeBadge, false);
                $widgetData->setNoPadding()->addClass('mb-3');
                $widgetData->setIcon('ti ti-layers');
                $tableData = $widgetData->addTable();
                $tableData->setDataFromArray($changedData)->setApplyDataTable(false)->setAjax(false);
                $tableData->addColumn('key')->setLabel('Field');
                $tableData->addColumn('before')->setLabel('Before')->addTransform('showMore:50');
                $tableData->addColumn('after')->setLabel('After')->addTransform('showMore:50');
                $tableData->addColumn('isChanged')->setLabel('Changed')->setCallback(function ($row, $val) {
                    return  $val ? '<span class="badge badge-success">' . 'YES' . '</span>' : '<span class="badge badge-danger">' . 'NO' . '</span>';
                });
            }
        }

        return $app;
    }

    private function transformToString($value) {
        if ($value instanceof CCarbon) {
            $value = (string) $value;
        }
        if (is_array($value)) {
            $value = json_encode($value);
        }
        if (is_object($value)) {
            $value = json_encode($value);
        }

        return $value;
    }
}
