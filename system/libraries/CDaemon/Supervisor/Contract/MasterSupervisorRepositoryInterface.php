<?php

interface CDaemon_Supervisor_Contract_MasterSupervisorRepositoryInterface {
    /**
     * Get the names of all the master supervisors currently running.
     *
     * @return array
     */
    public function names();

    /**
     * Get information on all of the master supervisors.
     *
     * @return array
     */
    public function all();

    /**
     * Get information on a master supervisor by name.
     *
     * @param string $name
     *
     * @return array
     */
    public function find($name);

    /**
     * Get information on the given master supervisors.
     *
     * @param array $names
     *
     * @return array
     */
    public function get(array $names);

    /**
     * Update the information about the given master supervisor.
     *
     * @param \CDaemon_Supervisor_MasterSupervisor $master
     *
     * @return void
     */
    public function update(CDaemon_Supervisor_MasterSupervisor $master);

    /**
     * Remove the master supervisor information from storage.
     *
     * @param string $name
     *
     * @return void
     */
    public function forget($name);

    /**
     * Remove expired master supervisors from storage.
     *
     * @return void
     */
    public function flushExpired();
}
