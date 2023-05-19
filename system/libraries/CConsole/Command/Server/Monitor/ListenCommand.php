<?php

class CConsole_Command_Server_Monitor_ListenCommand extends CConsole_Command {
    use CConsole_Command_Server_Monitor_Trait_MessageCreatorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:monitor:listen {resources?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'monitor memory usage, cpu usage, network connection and nginx status';

    /**
     * @var CServer_Monitor_Cpu
     */
    private $cpu;

    /**
     * @var CServer_Monitor_Memory
     */
    private $memory;

    /**
     * @var CServer_Monitor_HardDisk
     */
    private $hardDisk;

    public function __construct() {
        $this->cpu = new CServer_Monitor_Cpu();
        $this->memory = new CServer_Monitor_Memory();
        $this->hardDisk = new CServer_Monitor_HardDisk();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $resources = c::collect($this->argument('resources'));

        $resourcesIsEmpty = $resources->isEmpty();

        $this->line(
            $this->timeMessage()
        );
        if ($resources->contains('cpu') || $resourcesIsEmpty) {
            $this->info(
                $this->cpuMessage($this->cpu->check())
            );
        }

        if ($resources->contains('memory') || $resourcesIsEmpty) {
            $this->info(
                $this->memoryMessage($this->memory->check())
            );
        }

        if ($resources->contains('hdd') || $resourcesIsEmpty) {
            $this->info(
                $this->hardDiskMessage($this->hardDisk->check())
            );
        }
    }
}
