<?php

trait CTrait_Controller_Application_Log_System {
    public function system() {
        $app = c::app();
        $path = DOCROOT . 'logs/' . cf::appCode();
        $years = array_reverse($this->getSubdirectory($path));

        $reportTimes = [];

        foreach ($years as $year) {
            $months = array_reverse($this->getSubdirectory("${path}/${year}"));
            foreach ($months as $month) {
                $reportTimes[] = [
                    'year' => $year,
                    'month' => $month
                ];
            }
        }

        $tabList = $app->addTabList();
        foreach ($reportTimes as $time) {
            $year = carr::get($time, 'year');
            $month = carr::get($time, 'month');
            $tab = $tabList->addTab()
                ->setLabel("${year} - ${month}")
                ->addClass('p-0')
                ->setAjaxUrl($this->currentUrl() . "logSystem/${year}/${month}");
        }

        return $app;
    }

    private function currentUrl() {
        $class = get_called_class();

        $classExplode = explode('_', $class);
        $classExplode = array_map(function ($item) {
            return cstr::camel($item);
        }, $classExplode);
        $url = curl::base() . implode('/', array_slice($classExplode, 1)) . '/';

        return $url;
    }

    private function getSubdirectory($path) {
        return array_values(array_diff(scandir($path), ['..', '.']));
    }

    public function logSystem($year = null, $month = null) {
        $app = c::app();
        $path = DOCROOT . 'logs/' . cf::appCode();
        if ($year) {
            $path .= DS . $year;

            if ($month) {
                $path .= DS . $month;
            }
        }
        $phpFiles = [];

        try {
            $directory = new RecursiveDirectoryIterator($path);
            $iterator = new RecursiveIteratorIterator($directory);
            $phpFiles = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
        } catch (Exception $ex) {
        }

        $files = [];
        $report = [];
        foreach ($phpFiles as $file) {
            $files[] = carr::get($file, '0');
        }
        $files = c::collect($files)->sort()->toArray();
        foreach ($files as $file) {
            $fileContent = file_get_contents($file);
            $lines = explode("\n", $fileContent);

            foreach ($lines as $line) {
                preg_match('#^(.*?) --- (.*?):(.*?): (.*?)$#ims', $line, $matches);

                if ($matches) {
                    $time = carr::get($matches, 1);
                    $domain = carr::get($matches, 2);
                    $status = carr::get($matches, 3);
                    $message = carr::get($matches, 4);
                    $filename = str_replace('.php', '', basename($file));
                    $report[] = [
                        'time' => $time,
                        'domain' => $domain,
                        'status' => $status,
                        'message' => $message,
                        'filename' => $filename,
                    ];
                }
            }
        }

        $report = array_reverse($report);
        $table = $app->addTable()->setDataFromArray($report);
        $table->addColumn('time')->setLabel('Time');
        $table->addColumn('domain')->setLabel('Domain');
        $table->addColumn('status')->setLabel('Status');
        $table->addColumn('message')->setLabel('Message');
        $table->addRowAction()->setLabel('View Raw')->addClass('btn-primary')->setLink($this->controllerUrl() . 'systemRaw/{filename}');
        //$table->addHeaderAction()->setLabel('View Raw')->addClass('btn-primary')->setLink($this->controllerUrl() . 'systemRaw/' . $year . '/' . $month);

        return $app;
    }

    public function systemRaw($file) {
        $app = c::app();
        $path = DOCROOT . 'logs/' . cf::appCode();
        $year = substr($file, 0, 4);
        $month = substr($file, 4, 2);
        if ($year) {
            $path .= DS . $year;

            if ($month) {
                $path .= DS . $month;
            }
        }
        $file = $path . DS . $file . '.php';
        $content = file_get_contents($file);
        $app->addPre()->add($content);

        return $app;
    }
}
