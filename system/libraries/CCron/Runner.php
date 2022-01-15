<?php

use Symfony\Component\Console\Output\OutputInterface;

class CCron_Runner {
    protected $eventsRan;

    protected $startedAt;

    public function run(OutputInterface $output = null) {
        $this->eventsRan = false;
        $this->startedAt = c::now();
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
            if ($output != null) {
                $output->writeln('No scheduled commands are ready to run.');
            }
        }
    }

    /**
     * Run the given single server event.
     *
     * @param \CCron_Event $event
     *
     * @return void
     */
    protected function runSingleServerEvent($event, OutputInterface $output = null) {
        if (c::cron()->serverShouldRun($event, $this->startedAt)) {
            $this->runEvent($event);
        } else {
            if ($output) {
                $output->writeln('<info>Skipping command (has already run on another server):</info> ' . $event->getSummaryForDisplay());
            }
        }
    }

    /**
     * Run the given event.
     *
     * @param \CCron_Event $event
     *
     * @return void
     */
    protected function runEvent($event, OutputInterface $output = null) {
        CCron::setEvent($event);
        if ($output) {
            $output->writeln('<info>[' . date('c') . '] Running scheduled command:</info> ' . $event->getSummaryForDisplay());
        }

        CEvent::dispatch(new CCron_Event_ScheduledTaskStarting($event));
        $start = microtime(true);

        try {
            $event->run();
            $runtime = round(microtime(true) - $start, 2);
            $event->log('Run scheduled job : ' . $event->getSummaryForDisplay() . " [${runtime}s]");

            CEvent::dispatch(new CCron_Event_ScheduledTaskFinished(
                $event,
                $runtime
            ));

            $this->eventsRan = true;
        } catch (Throwable $e) {
            CEvent::dispatch(new CCron_Event_ScheduledTaskFailed($event, $e));
            CException::exceptionHandler()->report($e);
            $event->log();
            $event->log(str_repeat('#', 64));
            $event->log('# Error : ' . $e->getMessage() . "\n# " . $e->getFile() . ' (' . $e->getLine() . ')');
            $event->log(str_repeat('#', 64));
            $event->log();
        } catch (Exception $e) {
            CEvent::dispatch(new CCron_Event_ScheduledTaskFailed($event, $e));
            CException::exceptionHandler()->report($e);
            $event->log();
            $event->log(str_repeat('#', 64));
            $event->log('# Error : ' . $e->getMessage() . "\n# " . $e->getFile() . ' (' . $e->getLine() . ')');
            $event->log(str_repeat('#', 64));
            $event->log();
        }
        CCron::unsetEvent();
    }
}
