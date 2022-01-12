<?php

class CConsole_Command_Cron_ScheduleTestCommand extends CConsole_Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cron:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a scheduled command';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $commands = c::cron()->events();

        $commandNames = [];

        foreach ($commands as $command) {
            $commandNames[] = $command->command ?: $command->getSummaryForDisplay();
        }

        $index = array_search($this->choice('Which command would you like to run?', $commandNames), $commandNames);

        $event = $commands[$index];

        $this->line('<info>[' . date('c') . '] Running scheduled command:</info> ' . $event->getSummaryForDisplay());

        $event->run();
    }
}
