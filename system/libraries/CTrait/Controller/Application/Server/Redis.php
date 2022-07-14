<?php

trait CTrait_Controller_Application_Server_Redis {
    public function index() {
        $config = CF::config('database.redis');
        $connections = c::collect($config)->filter(function ($conn) {
            return is_array($conn);
        })->mapWithKeys(function ($value, $key) {
            return [$key => [
                'connection' => $key,
                'data' => $value
            ]];
        });

        $app = c::app();
        $table = $app->addTable();
        $table->setDataFromArray($connections);
        $table->addColumn('connection')->setLabel('Connection');
        $table->addRowAction()->setLabel('Detail')->setIcon('ti ti-search')->addClass('btn-primary')
            ->setLink($this->controllerUrl() . 'connection/{connection}');

        return $app;
    }

    public function connection($connection) {
        $app = c::app();
        CManager::registerModule('chartjs');
        CManager::registerModule('toastr');
        CManager::registerModule('themify-icons');

        $app->addBreadcrumb('Redis', $this->controllerUrl());
        $app->title($connection);

        $manager = CRedis_Manager::instance($connection);

        $info = $manager->getInformation();
        $widget = $app->addWidget()->setTitle('Redis ' . $connection)->addClass('mb-3');
        $form = $widget->addForm();
        $row = $form->addDiv()->addClass('row');
        if (isset($info['Server'])) {
            $info = carr::get($info, 'Server');
        }
        $row->addDiv()->addClass('col-md-3 col-sm-6')->addField()->setLabel('Connection Name')->addLabelControl()->setValue('<code>' . $connection . '</code>');
        $row->addDiv()->addClass('col-md-3 col-sm-6')->addField()->setLabel('Redis Version')->addLabelControl()->setValue('<code>' . carr::get($info, 'redis_version') . '</code>');
        $tab = c::request()->tab;
        $tabList = $app->addTabList()->setTabPosition('top');

        $tabInfo = $tabList->addTab()->setLabel('Info');
        $tabInfo->setNoPadding()->setActive($tab == 'info');
        $tabInfo->setAjaxUrl($this->controllerUrl() . 'tab/info/index/' . $connection);

        $tabKeys = $tabList->addTab()->setLabel('Keys');
        $tabKeys->setNoPadding()->setActive($tab == 'keys');
        $tabKeys->setAjaxUrl($this->controllerUrl() . 'tab/keys/index/' . $connection);
        $tabMetrics = $tabList->addTab()->setLabel('Metrics');
        $tabMetrics->setNoPadding()->setActive($tab == 'metrics');
        $tabMetrics->setAjaxUrl($this->controllerUrl() . 'tab/metrics/index/' . $connection);

        return $app;
    }

    public function tab($method, $submethod, $connection) {
        if ($method == 'info') {
            if ($submethod == 'index') {
                $app = c::app();
                $tabList = $app->addTabList()->setTabPosition('left');
                $tabList->addTab()->setLabel('Server')->setAjaxUrl($this->controllerUrl() . 'tab/info/server/' . $connection)->setNoPadding();
                $tabList->addTab()->setLabel('Memory')->setAjaxUrl($this->controllerUrl() . 'tab/info/memory/' . $connection)->setNoPadding();
                $tabList->addTab()->setLabel('CPU')->setAjaxUrl($this->controllerUrl() . 'tab/info/cpu/' . $connection)->setNoPadding();
                $tabList->addTab()->setLabel('Clients')->setAjaxUrl($this->controllerUrl() . 'tab/info/clients/' . $connection)->setNoPadding();
                $tabList->addTab()->setLabel('Replication')->setAjaxUrl($this->controllerUrl() . 'tab/info/replication/' . $connection)->setNoPadding();
                $tabList->addTab()->setLabel('Keyspace')->setAjaxUrl($this->controllerUrl() . 'tab/info/keyspace/' . $connection)->setNoPadding();
                $tabList->addTab()->setLabel('Persistence')->setAjaxUrl($this->controllerUrl() . 'tab/info/persistence/' . $connection)->setNoPadding();
                $tabList->addTab()->setLabel('Cluster')->setAjaxUrl($this->controllerUrl() . 'tab/info/cluster/' . $connection)->setNoPadding();

                return $app;
            }

            return $this->tabInfo($submethod, $connection);
        }

        if ($method == 'keys') {
            if ($submethod == 'index') {
                return $this->tabKeys($connection);
            }
        }
        if ($method == 'metrics') {
            if ($submethod == 'index') {
                $app = c::app();
                $tabList = $app->addTabList()->setTabPosition('left');
                $tabList->addTab()->setLabel('Memory')->setAjaxUrl($this->controllerUrl() . 'tab/metrics/memory/' . $connection)->setNoPadding();
                $tabList->addTab()->setLabel('CPU')->setAjaxUrl($this->controllerUrl() . 'tab/metrics/cpu/' . $connection)->setNoPadding();
                $tabList->addTab()->setLabel('Clients')->setAjaxUrl($this->controllerUrl() . 'tab/metrics/clients/' . $connection)->setNoPadding();
                $tabList->addTab()->setLabel('Throughput')->setAjaxUrl($this->controllerUrl() . 'tab/metrics/throughput/' . $connection)->setNoPadding();

                return $app;
            }
            if ($submethod == 'throughput') {
                return $this->tabThroughput($connection);
            }

            return $this->tabMetrics($submethod, $connection);
        }
    }

    protected function tabInfo($section, $connection) {
        $manager = CRedis_Manager::instance($connection);

        $data = $manager->getInformation($section);
        $app = c::app();
        $widget = $app->addWidget()->setTitle(ucfirst($section))->addClass('mb-3');
        $form = $widget->addForm();
        $row = $form->addDiv()->addClass('row');
        $ucfirstSection = ucfirst($section);
        if ($section == 'cpu') {
            $ucfirstSection = 'CPU';
        }
        if (isset($data[$ucfirstSection]) && is_array($data[$ucfirstSection])) {
            $data = $data[$ucfirstSection];
        }
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $row->addDiv()->addClass('col-md-3 col-sm-6')->addField()->setLabel($key)->addLabelControl()->setValue('<code>' . $value . '</code>');
        }

        return $app;
    }

    protected function tabKeys($connection) {
        $app = c::app();

        $pattern = '*';
        $widget = $app->addWidget()->addClass('mb-3');
        $widget->setTitle('Scan');

        $form = $widget->addForm();
        $div = $app->addDiv();

        $widget->addHeaderAction()->setLabel('Add Key')
            ->setIcon('ti ti-plus')
            ->setLink($this->controllerUrl() . 'keys/add/' . $connection . '')
            ->addClass('btn-primary');
        $form->addField()->setLabel('Pattern')->addTextControl('pattern')->setValue($pattern);

        $form->addActionList()->addAction()->setLabel('Scan')->addClass('btn-primary')
            ->onClickListener()
            ->addReloadHandler()
            ->setTarget($div)
            ->setUrl($this->controllerUrl() . 'ajax/scan/' . $connection)
            ->addParamInput('pattern');

        return $app;
    }

    public function ajax($method, $connection, $section = null) {
        if ($method == 'scan') {
            return $this->ajaxScan($connection);
        }
        if ($method == 'metrics') {
            return $this->ajaxMetrics($connection, $section);
        }
        if ($method == 'throughput') {
            return $this->ajaxThroughput($connection, $section);
        }
    }

    public function ajaxScan($connection) {
        $manager = CRedis_Manager::instance($connection);

        $pattern = c::request()->pattern;
        $result = $manager->scan($pattern);
        $app = c::app();

        $table = $app->addTable();
        $table->setDataFromArray($result->toArray());
        $table->addColumn('key')->setLabel('Key');
        $table->addColumn('type')->setLabel('Type');
        $table->addColumn('ttl')->setLabel('TTL');
        $table->setRowActionStyle('btn-dropdown');
        $table->addRowAction()->setLabel('Edit')->setIcon('ti ti-pencil')->setLink($this->controllerUrl() . 'keys/edit/' . $connection . '/{key}');
        $table->addRowAction()->setLabel('Delete')->setIcon('ti ti-trash')->setLink($this->controllerUrl() . 'keys/delete/' . $connection . '/{key}')->setConfirm();

        return $app;
    }

    public function keys($method, $connection, $key = null) {
        if ($method == 'add' || $method == 'edit') {
            return $this->keysEdit($connection, $key);
        }
    }

    public function keysEdit($connection, $type = null, $key = null) {
        $isAdd = $key == null;
        $value = '';
        if ($key == null) {
            if (c::request()->has('key')) {
                $key = c::request()->input('key');
            }
        }
        if ($type == null) {
            if (c::request()->has('type')) {
                $key = c::request()->input('type');
            }
        }
        if (c::request()->has('value')) {
            //do post the value
        }
        $app = c::app();
        $app->addBreadcrumb('Redis', $this->controllerUrl());
        $app->addBreadcrumb($connection, $this->controllerUrl() . 'connection/' . $connection . '?tab=keys');
        $app->title($isAdd ? 'Add Key' : 'Edit Key ' . $key);

        $widget = $app->addWidget()->setIcon($isAdd ? 'ti ti-plus' : 'ti ti-pencil');
        $widget->setTitle($isAdd ? 'Add Key' : 'Edit Key ' . $key);
        $form = $widget->addForm();
        $form->setAction($this->controllerUrl() . 'keys/' . ($isAdd ? 'add' : 'edit') . '/' . $connection . ($isAdd ? '' : '/' . $key));

        $form->when($isAdd, function (CElement_Component_Form $form) use ($key, $type) {
            $form->addField()->setLabel('Key')->addTextControl('key')->setValue($key);
            $typeList = [
                'string' => 'STRING',
                'hash' => 'HASH',
                'list' => 'LIST',
                'set' => 'SET',
                'zset' => 'ZSET',

            ];
            $form->addField()->setLabel('Type')->addSelectControl('type')->setValue($type)->setList($typeList);
        }, function (CElement_Component_Form $form) use ($key, $type) {
            $form->addField()->setLabel('Key')->addLabelControl('key')->setValue($key);
            $form->addField()->setLabel('Type')->addLabelControl('type')->setValue($type);
        });

        $form->addField()->setLabel('Value')->addTextControl('value')->setValue($value);

        $form->addActionList()->addAction()->setLabel('Submit')->setSubmit();

        return $app;
    }

    protected function tabMetrics($section, $connection) {
        $datasetMap = [
            'memory' => [
                'used_memory' => [
                    'key' => 'used_memory',
                    'color' => CColor::fromString('used_memory', ['luminosity' => 'dark'])->toRgb()->toCssStyle(),
                ],
                'used_memory_rss' => [
                    'key' => 'used_memory_rss',
                    'color' => CColor::fromString('used_memory_rss', ['luminosity' => 'dark'])->toRgb()->toCssStyle(),
                ],
                'used_memory_peak' => [
                    'key' => 'used_memory_peak',
                    'color' => CColor::fromString('used_memory_peak', ['luminosity' => 'dark'])->toRgb()->toCssStyle(),
                ],
            ],
            'cpu' => [
                'used_cpu_user' => [
                    'key' => 'used_cpu_user',
                    'color' => CColor::fromString('used_cpu_user', ['luminosity' => 'dark'])->toRgb()->toCssStyle(),
                ],
                'used_cpu_sys' => [
                    'key' => 'used_cpu_sys',
                    'color' => CColor::fromString('used_cpu_sys', ['luminosity' => 'dark'])->toRgb()->toCssStyle(),
                ],
            ],
            'clients' => [
                'connected_clients' => [
                    'key' => 'connected_clients',
                    'color' => CColor::fromString('connected_clients', ['luminosity' => 'dark'])->toRgb()->toCssStyle(),
                ],
                'blocked_clients' => [
                    'key' => 'blocked_clients',
                    'color' => CColor::fromString('blocked_clients', ['luminosity' => 'dark'])->toRgb()->toCssStyle(),
                ],
            ],
        ];
        $app = c::app();
        $app->addView('cresenity.manager.services.redis.metrics', [
            'section' => $section,
            'connection' => $connection,
            'datasets' => carr::get($datasetMap, $section, []),
            'pollingUrl' => $this->controllerUrl() . 'ajax/metrics/' . $connection . '/' . $section . '',
        ]);

        return $app;
    }

    protected function tabThroughput($connection) {
        $app = c::app();
        $app->addView('cresenity.manager.services.redis.throughput', [
            'connection' => $connection,
            'pollingUrl' => $this->controllerUrl() . 'ajax/throughput/' . $connection . '',
        ]);

        return $app;
    }

    public function ajaxMetrics($connection, $section) {
        $manager = CRedis_Manager::instance($connection);
        $data = $manager->getInformation($section);
        $ucfirstSection = ucfirst($section);
        if ($section == 'cpu') {
            $ucfirstSection = 'CPU';
        }
        if (isset($data[$ucfirstSection]) && is_array($data[$ucfirstSection])) {
            $data = $data[$ucfirstSection];
        }
        if ($section == 'memory') {
            $data['used_memory'] = CRedis_Formatter_Information::formatBytes($data['used_memory']);
            $data['used_memory_rss'] = CRedis_Formatter_Information::formatBytes($data['used_memory_rss']);
            $data['used_memory_peak'] = CRedis_Formatter_Information::formatBytes($data['used_memory_peak']);
        }
        $responseData = [
            'errCode' => 0,
            'errMessage' => '',
            'data' => $data,
        ];

        return c::response()->json($responseData);
    }

    public function ajaxThroughput($connection) {
        $manager = CRedis_Manager::instance($connection);
        $data = $manager->getInformation('commandstats');

        $ucfirstSection = ucfirst('commandstats');
        if (isset($data[$ucfirstSection]) && is_array($data[$ucfirstSection])) {
            $data = $data[$ucfirstSection];
        }
        $responseData = [
            'errCode' => 0,
            'errMessage' => '',
            'data' => $data,
        ];

        return c::response()->json($responseData);
    }
}
