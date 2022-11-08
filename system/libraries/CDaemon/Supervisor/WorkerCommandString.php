<?php

class CDaemon_Supervisor_WorkerCommandString {
    /**
     * The base worker command.
     *
     * @var string
     */
    public static $command = 'exec @php phpcf daemon:supervisor:work';

    /**
     * Get the command-line representation of the options for a worker.
     *
     * @param \CDaemon_Supervisor_SupervisorOptions $options
     *
     * @return string
     */
    public static function fromOptions(CDaemon_Supervisor_SupervisorOptions $options) {
        $command = str_replace('@php', CDaemon_Supervisor_PhpBinary::path(), static::$command);

        return sprintf(
            "%s {$options->connection} %s",
            $command,
            static::toOptionsString($options)
        );
    }

    /**
     * Get the additional option string for the command.
     *
     * @param \CDaemon_Supervisor_SupervisorOptions $options
     *
     * @return string
     */
    public static function toOptionsString(CDaemon_Supervisor_SupervisorOptions $options) {
        return CDaemon_Supervisor_QueueCommandString::toWorkerOptionsString($options);
    }

    /**
     * Reset the base command back to its default value.
     *
     * @return void
     */
    public static function reset() {
        static::$command = 'exec @php artisan daemon:supervisor:work';
    }
}
