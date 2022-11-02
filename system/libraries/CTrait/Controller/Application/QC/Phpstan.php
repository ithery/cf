<?php

/**
 * Description of phpstan.
 *
 * @author Hery
 */
trait CTrait_Controller_Application_QC_Phpstan {
    protected function getTitle() {
        return 'Php Stan';
    }

    public function index() {
        $app = c::app();

        $app->title($this->getTitle());

        $phpstan = CQC::phpstan();

        if (!$phpstan->isInstalled()) {
            $app->addAlert()->setType('error')->add('phpstan is not installed, please run phpstan:install command');

            return $app;
        }
        $runUrl = $this->controllerUrl() . 'run';
        $pollUrl = $this->controllerUrl() . 'poll';
        $data = CQC::phpstan()->createRunner()->getData();

        $app->addView('cresenity.qc.phpstan', [
            'data' => CQC::phpstan()->createRunner()->getData(),
            'runUrl' => $runUrl,
            'pollUrl' => $pollUrl,
        ]);
        $result = carr::get($data, 'result');
        if (is_array($result)) {
            $tableData = c::collect(carr::get($result, 'files'))->map(function ($item, $key) {
                $file = $key;
                if (cstr::startsWith($file, DOCROOT)) {
                    $file = str_replace(DOCROOT, '', $file);
                }

                return [
                    'file' => $file,
                    'errors' => carr::get($item, 'errors'),
                    // 'messages' => c::collect(carr::get($item, 'messages'))->filter(function ($message) {
                    //     return carr::get($message, 'ignorable') === true;
                    // })->toArray()
                    'messages' => carr::get($item, 'messages'),
                ];
            })->filter(function ($row) {
                return count(carr::get($row, 'messages')) > 0;
            })->toArray();
            $table = $app->addTable();
            $table->setDataFromArray($tableData);
            $table->addColumn('file')->setLabel('File');
            $table->addColumn('errors')->setLabel('Error');
            $table->addColumn('messages')->setLabel('Message')->setCallback(function ($row, $value) {
                $div = c::div();
                if (is_array($value)) {
                    $ul = $div->addUl();
                    foreach ($value as $val) {
                        $line = carr::get($val, 'line');
                        $message = carr::get($val, 'message');
                        $ul->addLi()->add('line ' . $line . ':' . $message . (carr::get($val, 'ignorable') ? ' (ignorable)' : ' (not ignorable)'));
                    }
                } else {
                    $div->add($value);
                }

                return $div;
            });
        }

        return $app;
    }

    public function run() {
        CQC::phpstan()->createRunner()->run();

        return CApp_Base::toJsonResponse(0, '', []);
    }

    public function poll() {
        $data = CQC::phpstan()->createRunner()->getData();

        return CApp_Base::toJsonResponse(0, '', $data);
    }
}
