<?php

/**
 * Description of DbSyncCommand.
 */
class CDevSuite_Command_DbSyncCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{--from=} {--to=} {--force=}';
    }

    /**
     * Compare the "from" and "to" databases and, after confirmation (unless forced), sync the schema.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        $from = $cfCommand->option('from');
        $to = $cfCommand->option('to');
        $force = $cfCommand->option('force');
        CDevSuite::db()->existsOrExit($from);
        CDevSuite::db()->existsOrExit($to);

        CDevSuite::db()->compare($from, $to);
        $choice = true;
        if (!$force) {
            $choice = $cfCommand->confirm('Are you sure execute sql on ' . $to . ':', false);
        }
        if ($choice) {
            CDevSuite::db()->sync($from, $to);
        } else {
            CDevSuite::info('User cancelled command');
        }
    }
}
