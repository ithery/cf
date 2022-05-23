<?php

class CConsole_Command_Cron_ScheduleRunCommand extends CConsole_Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cron:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the scheduled commands';

    /**
     * The 24 hour timestamp this scheduler command started running.
     *
     * @var \CCarbon
     */
    protected $startedAt;

    /**
     * Check if any events ran.
     *
     * @var bool
     */
    protected $eventsRan = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->startedAt = c::now();

        parent::__construct();
    }

    public function handle() {
        foreach (c::cron()->dueEvents() as $event) {
            /** @var CCron_Event $event */
            if (!$event->filtersPass()) {
                CEvent::dispatch(new CCron_Event_ScheduledTaskSkipped($event));

                continue;
            }

            if ($event->onOneServer) {
                $this->runSingleServerEvent($event);
            } else {
                $this->runEvent($event);
            }

            $this->eventsRan = true;
        }

        if (!$this->eventsRan) {
            $this->info('No scheduled commands are ready to run.');
        }
    }

    /**
     * Run the given single server event.
     *
     * @param \CCron_Event $event
     *
     * @return void
     */
    protected function runSingleServerEvent($event) {
        if (c::cron()->serverShouldRun($event, $this->startedAt)) {
            $this->runEvent($event);
        } else {
            $this->line('<info>Skipping command (has already run on another server):</info> ' . $event->getSummaryForDisplay());
        }
    }

    /**
     * Run the given event.
     *
     * @param \CCron_Event $event
     *
     * @return void
     */
    protected function runEvent($event) {
        $this->line('<info>[' . date('c') . '] Running scheduled command:</info> ' . $event->getSummaryForDisplay());

        CEvent::dispatch(new CCron_Event_ScheduledTaskStarting($event));

        $start = microtime(true);

        try {
            $event->run();

            CEvent::dispatch(new CCron_Event_ScheduledTaskFinished(
                $event,
                round(microtime(true) - $start, 2)
            ));

            $this->eventsRan = true;
        } catch (Throwable $e) {
            CEvent::dispatch(new CCron_Event_ScheduledTaskFailed($event, $e));

            CException::exceptionHandler()->report($e);
        } catch (Exception $e) {
            CEvent::dispatch(new CCron_Event_ScheduledTaskFailed($event, $e));

            CException::exceptionHandler()->report($e);
        }
    }
}
