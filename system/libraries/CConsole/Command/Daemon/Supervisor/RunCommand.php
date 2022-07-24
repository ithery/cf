<?php

class CConsole_Command_Daemon_Supervisor_RunCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daemon:supervisor:run {--environment= : The environment name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a master supervisor in the foreground';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $masters = CDaemon::supervisor()->masterSupervisorRepository();
        if ($masters->find(CDaemon_Supervisor_MasterSupervisor::name())) {
            return $this->comment('A master supervisor is already running on this machine.');
        }

        $master = (new CDaemon_Supervisor_MasterSupervisor())->handleOutputUsing(function ($type, $line) {
            $this->output->write($line);
        });

        CDaemon_Supervisor_ProvisioningPlan::get(CDaemon_Supervisor_MasterSupervisor::name())->deploy(
            $this->option('environment') ?? CF::config('daemon.supervisor.env') ?? CF::config('daemon.supervisor.env')
        );

        $this->info('Supervisor started successfully.');

        pcntl_async_signals(true);

        pcntl_signal(SIGINT, function () use ($master) {
            $this->line('Shutting down...');

            return $master->terminate();
        });

        $master->monitor();
    }
}
