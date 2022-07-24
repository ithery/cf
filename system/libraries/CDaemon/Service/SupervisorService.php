<?php

class CDaemon_Service_SupervisorService extends CDaemon_ServiceAbstract {
    protected $loopInterval = 0.1;

    protected $environment = 'development';

    public function setup() {
        $masters = CDaemon::supervisor()->masterSupervisorRepository();
        if ($masters->find(CDaemon_Supervisor_MasterSupervisor::name())) {
            return CDaemon::log('A master supervisor is already running on this machine.');
        }

        $master = (new CDaemon_Supervisor_MasterSupervisor())->handleOutputUsing(function ($type, $line) {
            CDaemon::log($line);
        });

        CDaemon_Supervisor_ProvisioningPlan::get(CDaemon_Supervisor_MasterSupervisor::name())->deploy(
            $this->environment ?? CF::config('daemon.supervisor.env') ?? CF::config('app.env')
        );

        $this->info('Supervisor started successfully.');

        pcntl_async_signals(true);

        pcntl_signal(SIGINT, function () use ($master) {
            $this->line('Shutting down...');

            return $master->terminate();
        });

        $master->monitor();
    }

    public function line($message) {
        CDaemon::log($message);
    }

    public function info($message) {
        $this->line('INFO:' . $message);
    }

    public function execute() {
    }
}
