<?php

use Cron\CronExpression;

trait CTrait_Controller_Application_Manager_Cron {
    protected function getTitle() {
        return 'Cron Manager';
    }

    public function index() {
        $app = c::app();
        $rows = [];

        foreach (c::cron()->events() as $event) {
            /** @var CCron_Event $event */
            $rows[] = [
                'id' => $this->getEventId($event),
                'command' => $event->getSummaryForDisplay(),
                'expression' => $event->expression,
                'description' => $event->description,
                'next_run_at' => (new CronExpression($event->expression))
                    ->getNextRunDate(CCarbon::now()->setTimezone($event->timezone))
                    ->setTimezone(CF::config('app.timezone'))
                    ->format('Y-m-d H:i:s P'),
            ];
        }

        usort($rows, function ($a, $b) {
            return $a['expression'] <=> $b['expression'];
        });

        $app->title($this->getTitle());
        $table = $app->addTable();
        $table->setTitle('Cron List');
        $table->setDataFromArray($rows);
        // $table->addColumn('command')->setLabel('Task');
        $table->addColumn('expression')->setLabel('Expression');
        $table->addColumn('description')->setLabel('Description');
        $table->addColumn('next_run_at')->setLabel('Next Run At');
        $table->addRowAction()
            ->setIcon('ti ti-search')
            ->addClass('btn-info')
            ->setLink(static::controllerUrl() . 'log/{id}');
        $table->setApplyDataTable(false);

        return $app;
    }

    public function log($eventId) {
        $app = c::app();
        $event = $this->getEvent($eventId);

        if ($event == null) {
            c::msg('error', 'Event not Found');

            return c::redirect(static::controllerUrl());
        }
        $logFile = $event->getLogFile();

        $app->setTitle($event->description . ' Log');
        $app->addBreadcrumb('Cron', static::controllerUrl());
        $actionContainer = $app->addDiv()->addClass('action-container mb-3');
        $rotateAction = $actionContainer->addAction()
            ->setLabel('Dump Status')
            ->addClass('btn-primary')
            ->setIcon('fas fa-sync')
            ->setLink(static::controllerUrl() . 'rotateLog/' . $eventId)
            ->setConfirm();

        $tabList = $app->addTabList()->setAjax(true);

        if (file_exists($logFile)) {
            $tabList->addTab()
                ->setLabel('Current')
                ->setAjaxUrl(static::controllerUrl() . 'logFile/' . $eventId);
        }

        for ($i = 1; $i <= 10; $i++) {
            $logFileRotate = $logFile . '.' . $i;
            if (file_exists($logFileRotate)) {
                $tabList->addTab()
                    ->setLabel('Rotate:' . $i)
                    ->setAjaxUrl(static::controllerUrl() . 'logFile/' . $eventId . '/' . $i);
            }
        }

        return $app;
    }

    public function logFile($eventId, $rotation = null) {
        $app = c::app();
        $event = $this->getEvent($eventId);

        if ($event == null) {
            c::msg('error', 'Event not Found');

            return c::redirect(static::controllerUrl());
        }
        $content = '';
        $logFile = $event->getLogFile();

        if ($rotation) {
            $logFile .= ".${rotation}";
        }

        if (!file_exists($logFile)) {
            return $app;
        }

        try {
            $content = file_get_contents($logFile);
        } catch (Exception $ex) {
            c::msg('error', $ex->getMessage());
        }

        $container = $app->addDiv()->addClass('console');

        $container->add($content);

        return $app;
    }

    public function rotateLog($eventId) {
        $event = $this->getEvent($eventId);

        if ($event == null) {
            c::msg('error', 'Event not Found');

            return c::redirect(static::controllerUrl());
        }

        $event->rotate();

        return c::redirect(static::controllerUrl() . 'log/' . $eventId);
    }

    private function getEvent($eventId) {
        foreach (c::cron()->events() as $event) {
            $id = $this->getEventId($event);
            if ($eventId == $id) {
                return $event;
            }
        }

        return null;
    }

    private function getEventId(CCron_Event $event) {
        return spl_object_id($event);
    }
}
