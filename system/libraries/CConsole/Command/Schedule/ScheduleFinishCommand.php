<?php

class CConsole_Command_Schedule_ScheduleFinishCommand extends CConsole_Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'schedule:finish {id} {code=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle the completion of a scheduled command';

    /**
     * Indicates whether the command should be shown in the Artisan command list.
     *
     * @var bool
     */
    protected $hidden = true;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        c::collect(CConsole::schedule()->events())->filter(function ($value) {
            return $value->mutexName() == $this->argument('id');
        })->each(function (CConsole_Schedule_Event $event) {
            $event->callafterCallbacksWithExitCode($this->laravel, $this->argument('code'));

            CEvent::dispatcher()->dispatch(new CConsole_Schedule_Event_ScheduledBackgroundTaskFinished($event));
        });
    }
}
