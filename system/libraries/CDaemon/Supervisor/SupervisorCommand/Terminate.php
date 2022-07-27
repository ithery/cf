<?php

class CDaemon_Supervisor_SupervisorCommand_Terminate {
    /**
     * Process the command.
     *
     * @param \CDaemon_Contract_TerminableInterface $terminable
     * @param array                                 $options
     *
     * @return void
     */
    public function process(CDaemon_Contract_TerminableInterface $terminable, array $options) {
        $terminable->terminate($options['status'] ?? 0);
    }
}
