<?php
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CConsole_Command_Queue_ClearCommand extends CConsole_Command {
    use CConsole_Trait_ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all of the jobs from the specified queue';

    /**
     * Execute the console command.
     *
     * @return null|int
     */
    public function handle() {
        if (!$this->confirmToProceed()) {
            return 1;
        }

        $connection = $this->argument('connection')
                        ?: CF::config('queue.default');

        // We need to get the right queue for the connection which is set in the queue
        // configuration file for the application. We will pull it based on the set
        // connection being run for the queue operation currently being executed.
        $queueName = $this->getQueue($connection);

        $queue = ($this->laravel['queue'])->connection($connection);

        if ($queue instanceof CQueue_Contract_ClearableQueueInterface) {
            $count = $queue->clear($queueName);

            $this->line('<info>Cleared ' . $count . ' jobs from the [' . $queueName . '] queue</info> ');
        } else {
            $this->line('<error>Clearing queues is not supported on [' . (new ReflectionClass($queue))->getShortName() . ']</error> ');
        }

        return 0;
    }

    /**
     * Get the queue name to clear.
     *
     * @param string $connection
     *
     * @return string
     */
    protected function getQueue($connection) {
        return $this->option('queue') ?: CF::config(
            "queue.connections.{$connection}.queue",
            'default'
        );
    }

    /**
     *  Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return [
            ['connection', InputArgument::OPTIONAL, 'The name of the queue connection to clear'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return [
            ['queue', null, InputOption::VALUE_OPTIONAL, 'The name of the queue to clear'],

            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
        ];
    }
}
