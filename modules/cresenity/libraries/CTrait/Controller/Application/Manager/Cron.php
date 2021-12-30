<?php

use Cron\CronExpression;

trait CTrait_Controller_Application_Manager_Cron {
    protected function getTitle() {
        return 'Cron Manager';
    }

    public function index() {
        $app = CApp::instance();
        $db = CDatabase::instance();
        $rows = [];

        foreach (c::cron()->events() as $event) {
            /** @var CCron_Event $event */
            $rows[] = [
                'command' => $event->getSummaryForDisplay(),
                'expression' => $event->expression,
                'description' => $event->description,
                'next_run_at' => (new CronExpression($event->expression))
                    ->getNextRunDate(CCarbon::now()->setTimezone($event->timezone))
                    ->setTimezone(CF::config('app.timezone'))
                    ->format('Y-m-d H:i:s P'),
            ];
        }

        $app->title($this->getTitle());
        $table = $app->addTable();
        $table->setDataFromArray($rows);
        $table->addColumn('command')->setLabel('Task');
        $table->addColumn('expression')->setLabel('Expression');
        $table->addColumn('description')->setLabel('Description');
        $table->addColumn('next_run_at')->setLabel('Next Run At');
        $table->setTitle('Cron List');
        $table->setApplyDataTable(false);

        return $app;
    }
}
