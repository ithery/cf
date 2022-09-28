<?php

use Symfony\Component\Process\Process;

class CDaemon_Supervisor_MasterSupervisorCommand_AddSupervisor {
    /**
     * Process the command.
     *
     * @param \CDaemon_Supervisor_MasterSupervisor $master
     * @param array                                $options
     *
     * @return void
     */
    public function process(CDaemon_Supervisor_MasterSupervisor $master, array $options) {
        $options = CDaemon_Supervisor_SupervisorOptions::fromArray($options);

        $master->supervisors[] = new CDaemon_Supervisor_SupervisorProcess(
            $options,
            $this->createProcess($master, $options),
            function ($type, $line) use ($master) {
                $master->output($type, $line);
            }
        );
    }

    /**
     * Create the Symfony process instance.
     *
     * @param \CDaemon_Supervisor_MasterSupervisor  $master
     * @param \CDaemon_Supervisor_SupervisorOptions $options
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function createProcess(CDaemon_Supervisor_MasterSupervisor $master, CDaemon_Supervisor_SupervisorOptions $options) {
        $command = $options->toSupervisorCommand();

        return Process::fromShellCommandline($command, $options->directory ?? DOCROOT)
            ->setTimeout(null)
            ->disableOutput();
    }
}
