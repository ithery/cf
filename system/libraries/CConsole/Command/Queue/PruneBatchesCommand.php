<?php

class CConsole_Command_Queue_PruneBatchesCommand extends CConsole_Command {
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'queue:prune-batches
                {--hours=24 : The number of hours to retain batch data}
                {--unfinished= : The number of hours to retain unfinished batch data }
                {--cancelled= : The number of hours to retain cancelled batch data }';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var null|string
     *
     * @deprecated
     */
    protected static $defaultName = 'queue:prune-batches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune stale entries from the batches database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $repository = CQueue::batchRepository();

        $count = 0;

        if ($repository instanceof CQueue_Contract_PrunableBatchRepositoryInterface) {
            $count = $repository->prune(CCarbon::now()->subHours($this->option('hours')));
        }

        $this->info("{$count} entries deleted.");

        if ($this->option('unfinished')) {
            $count = 0;

            if ($repository instanceof CQueue_BatchRepository) {
                $count = $repository->pruneUnfinished(CCarbon::now()->subHours($this->option('unfinished')));
            }

            $this->info("{$count} unfinished entries deleted.");
        }

        if ($this->option('cancelled')) {
            $count = 0;

            if ($repository instanceof CQueue_BatchRepository) {
                $count = $repository->pruneCancelled(CCarbon::now()->subHours($this->option('cancelled')));
            }

            $this->info("{$count} cancelled entries deleted.");
        }
    }
}
