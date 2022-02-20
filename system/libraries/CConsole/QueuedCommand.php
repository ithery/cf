<?php

class CConsole_QueuedCommand implements CQueue_ShouldQueueInterface {
    use CQueue_Trait_DispatchableTrait, CQueue_Trait_QueueableTrait;

    /**
     * The data to pass to the Artisan command.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param array $data
     *
     * @return void
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Handle the job.
     *
     * @param \CConsole_KernelInterface $kernel
     *
     * @return void
     */
    public function handle(CConsole_KernelInterface $kernel) {
        $kernel->call(...array_values($this->data));
    }
}
