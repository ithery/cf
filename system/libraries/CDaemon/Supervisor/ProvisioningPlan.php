<?php

use Illuminate\Support\Str;
use Laravel\Horizon\Contracts\HorizonCommandQueue;
use Laravel\Horizon\Events\MasterSupervisorDeployed;
use Laravel\Horizon\MasterSupervisorCommands\AddSupervisor;

class CDaemon_Supervisor_ProvisioningPlan {
    /**
     * The master supervisor's name.
     *
     * @var string
     */
    public $master;

    /**
     * The raw provisioning plan.
     *
     * @var array
     */
    public $plan;

    /**
     * The parsed provisioning plan.
     *
     * @var array
     */
    public $parsed;

    /**
     * Create a new provisioning plan instance.
     *
     * @param string $master
     * @param array  $plan
     * @param array  $defaults
     *
     * @return void
     */
    public function __construct($master, array $plan, array $defaults = []) {
        $this->plan = $this->applyDefaultOptions($plan, $defaults);
        $this->master = $master;

        $this->parsed = $this->toSupervisorOptions();
    }

    /**
     * Get the current provisioning plan.
     *
     * @param string $master
     *
     * @return static
     */
    public static function get($master) {
        return new static($master, CF::config('daemon.supervisor.environments'), CF::config('daemon.supervisor.defaults', []));
    }

    /**
     * Apply the default supervisor options to each environment.
     *
     * @param array $plan
     * @param array $defaults
     *
     * @return array
     */
    protected function applyDefaultOptions(array $plan, array $defaults = []) {
        return c::collect($plan)->map(function ($plan) use ($defaults) {
            return array_replace_recursive($defaults, $plan);
        })->all();
    }

    /**
     * Get all of the defined environments for the provisioning plan.
     *
     * @return array
     */
    public function environments() {
        return array_keys($this->parsed);
    }

    /**
     * Determine if the provisioning plan has a given environment.
     *
     * @param string $environment
     *
     * @return bool
     */
    public function hasEnvironment($environment) {
        return array_key_exists($environment, $this->parsed);
    }

    /**
     * Deploy a provisioning plan to the current machine.
     *
     * @param string $environment
     *
     * @return void
     */
    public function deploy($environment) {
        $supervisors = c::collect($this->parsed)->first(function ($_, $name) use ($environment) {
            return cstr::is($name, $environment);
        });

        if (empty($supervisors)) {
            return;
        }

        foreach ($supervisors as $supervisor => $options) {
            $this->add($options);
        }

        c::event(new CDaemon_Supervisor_Event_MasterSupervisorDeployed($this->master));
    }

    /**
     * Add a supervisor with the given options.
     *
     * @param \CDaemon_Supervisor_SupervisorOptions $options
     *
     * @return void
     */
    protected function add(CDaemon_Supervisor_SupervisorOptions $options) {
        CDaemon_Supervisor::supervisorCommandQueue()->push(
            CDaemon_Supervisor_MasterSupervisor::commandQueueFor($this->master),
            CDaemon_Supervisor_MasterSupervisorCommand_AddSupervisor_AddSupervisor::class,
            $options->toArray()
        );
    }

    /**
     * Get the SupervisorOptions for a given environment and supervisor.
     *
     * @param string $environment
     * @param string $supervisor
     *
     * @return mixed
     */
    public function optionsFor($environment, $supervisor) {
        if (isset($this->parsed[$environment], $this->parsed[$environment][$supervisor])) {
            return $this->parsed[$environment][$supervisor];
        }
    }

    /**
     * Convert the provisioning plan into an array of SupervisorOptions.
     *
     * @return array
     */
    public function toSupervisorOptions() {
        return c::collect($this->plan)->mapWithKeys(function ($plan, $environment) {
            return [$environment => c::collect($plan)->mapWithKeys(function ($options, $supervisor) {
                return [$supervisor => $this->convert($supervisor, $options)];
            })];
        })->all();
    }

    /**
     * Convert the given array of options into a SupervisorOptions instance.
     *
     * @param string $supervisor
     * @param array  $options
     *
     * @return \CDaemon_Supervisor_SupervisorOptions
     */
    protected function convert($supervisor, $options) {
        $options = c::collect($options)->mapWithKeys(function ($value, $key) {
            $key = $key === 'tries' ? 'max_tries' : $key;
            $key = $key === 'processes' ? 'max_processes' : $key;
            $value = $key === 'queue' && is_array($value) ? implode(',', $value) : $value;
            $value = $key === 'backoff' && is_array($value) ? implode(',', $value) : $value;

            return [cstr::camel($key) => $value];
        })->all();

        if (isset($options['minProcesses']) && $options['minProcesses'] < 1) {
            throw new Exception("The value of [{$supervisor}.minProcesses] must be greater than 0.");
        }

        $options['parentId'] = getmypid();

        return CDaemon_Supervisor_SupervisorOptions::fromArray(
            carr::add($options, 'name', $this->master . ":{$supervisor}")
        );
    }
}
