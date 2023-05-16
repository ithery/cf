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
                ->setAjaxUrl($this->currentUrl() . "tabDaily/${year}/${month}");
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

    public function tabDaily($year, $month) {
        $app = c::app();
        $path = DOCROOT . 'logs/' . CF::appCode();

        $path .= DS . $year;

        $path .= DS . $month;
        $logFiles = [];

        try {
            $directory = new RecursiveDirectoryIterator($path);
            $iterator = new RecursiveIteratorIterator($directory);
            $logFiles = new RegexIterator($iterator, '/^.+\.log$/i', RecursiveRegexIterator::GET_MATCH);
        } catch (Exception $ex) {
        }
        $tabList = $app->addTabList();

        foreach ($logFiles as $file) {
            $file = carr::get($file, 0);

            if ($file) {
                $basename = basename($file);
                $tab = $tabList->addTab()
                    ->setLabel($basename)
                    ->addClass('p-0')
                    ->setAjaxUrl($this->currentUrl() . "systemTable/${year}/${month}/${basename}");
            }
        }

        return $app;
    }

    public function systemTable($year, $month, $file) {
        $app = c::app();
        $path = DOCROOT . 'logs/' . CF::appCode();

        $path .= DS . $year;

        $path .= DS . $month;
        $path .= DS . $file;
        $table = $app->addTable();
        $table->addColumn('time')->setLabel('Time');
        $table->addColumn('environment')->setLabel('Environment');
        $table->addColumn('level')->setLabel('Level')->setCallback(function ($row, $value) {
            return '<span class="badge badge-' . c::get($row, 'levelClass', 'secondary') . '">' . $value . '</span>';
        });
        $table->addColumn('message')->setLabel('Message')->setCallback(function ($row, $value) {
            return c::div()->addShowMore()->add($value);
        });
        $table->setDataFromClosure(function (CManager_DataProviderParameter $parameter) use ($path) {
            $errCode = 0;
            $errMessage = '';

            $perPage = $parameter->getPerPage();
            $page = $parameter->getPage();
            $keywords = $parameter->getSearchOrData();
            $logFile = CLogger::reader()->createLogFile($path);
            $logQuery = $logFile->logs();
            $keyword = carr::first($keywords);
            $logQuery->search($keyword);
            $logQuery->setDirection(CLogger_Reader_Direction::BACKWARD);

            return $logQuery->paginate($perPage, $page, function (CLogger_Reader_Log $log) {
                return [
                    'time' => (string) $log->time,
                    'environment' => $log->environment,
                    'level' => $log->level->getName(),
                    'levelClass' => $log->level->getClass(),
                    'short' => $log->text,
                    'message' => $log->fullText,
                ];
            });
        });
        $table->setAjax(true);

        return $app;
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
            $phpFiles = new RegexIterator($iterator, '/^.+[\.php|\.log]$/i', RecursiveRegexIterator::GET_MATCH);
        } catch (Exception $ex) {
        }

        $files = [];
        $report = [];
        foreach ($phpFiles as $file) {
            $files[] = carr::get($file, '0');
        }
        $files = c::collect($files)->sort()->toArray();
        foreach ($files as $file) {
            if (CFile::isDirectory($file)) {
                continue;
            }
            $extension = cstr::substr($file, -3);
            if ($extension == 'php') {
                $this->parseFromPhp($file, $report);
            }
            if ($extension == 'log') {
                $this->parseFromLog($file, $report);
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

    private function parseFromPhp($file, &$report) {
        $fileContent = file_get_contents($file);
        $lines = explode("\n", $fileContent);

        foreach ($lines as $line) {
            //[2023-05-16 20:27:41] development.DEBUG: MySQLi Database Driver Initialized
            preg_match('#^(.*?) (.*?):(.*?) (.*?)$#ims', $line, $matches);

            if ($matches) {
                $time = carr::get($matches, 1);
                $time = trim(str_replace(['[', ']'], '', $time));
                $domain = '';
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

    private function parseFromLog($file, &$report) {
        $logFile = CLogger::reader()->createLogFile($file);
        $logQuery = $logFile->logs();
        $query = '';
        $logQuery->search($query);
        $perPage = 10;
        $logs = $logQuery->paginate($perPage);
        cdbg::dd($logs);
    }

    public function systemRaw($file) {
        $app = c::app();
        $path = DOCROOT . 'logs/' . CF::appCode();
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
