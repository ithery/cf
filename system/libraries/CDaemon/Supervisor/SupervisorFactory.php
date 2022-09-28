<?php

class CDaemon_Supervisor_SupervisorFactory {
    /**
     * Create a new supervisor instance.
     *
     * @param \CDaemon_Supervisor_SupervisorOptions $options
     *
     * @return \CDaemon_Supervisor_Supervisor
     */
    public function make(CDaemon_Supervisor_SupervisorOptions $options) {
        return new CDaemon_Supervisor_Supervisor($options);
    }
}
