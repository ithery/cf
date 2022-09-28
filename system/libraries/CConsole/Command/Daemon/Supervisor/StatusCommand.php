<?php

use Laravel\Horizon\Contracts\MasterSupervisorRepository;

class CConsole_Command_Daemon_Supervisor_StatusCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daemon:supervisor:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the current status of Horizon';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $this->line($this->currentStatus(CDaemon::supervisor()->masterSupervisorRepository()));
    }

    /**
     * Get the current status of Horizon.
     *
     * @param \CDaemon_Supervisor_Contract_MasterSupervisorRepositoryInterface $masterSupervisorRepository
     *
     * @return string
     */
    protected function currentStatus(CDaemon_Supervisor_Contract_MasterSupervisorRepositoryInterface $masterSupervisorRepository) {
        if (!$masters = $masterSupervisorRepository->all()) {
            return 'Supervisor is inactive.';
        }

        return c::collect($masters)->contains(function ($master) {
            return $master->status === 'paused';
        }) ? 'Supervisor is paused.' : 'Supervisor is running.';
    }
}
