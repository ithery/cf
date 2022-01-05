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
            $path .= "/${year}";

            if ($month) {
                $path .= "/${month}";
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
        foreach ($files as $file) {
            $fileContent = file_get_contents($file);
            $lines = explode("\n", $fileContent);
            foreach ($lines as $line) {
                preg_match('/^(.*?) --- (.*?):(.*?): (.*?)$/', $line, $matches);
                if ($matches) {
                    $time = carr::get($matches, 1);
                    $domain = carr::get($matches, 2);
                    $status = carr::get($matches, 3);
                    $message = carr::get($matches, 4);

                    $report[] = [
                        'time' => $time,
                        'domain' => $domain,
                        'status' => $status,
                        'message' => $message
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

        return $app;
    }
}
