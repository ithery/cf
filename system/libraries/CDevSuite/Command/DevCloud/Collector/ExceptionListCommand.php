<?php

/**
 * Description of ExceptionListCommand.
 */
class CDevSuite_Command_DevCloud_Collector_ExceptionListCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        //not --env: CF already defines a global --env option (the environment
        //the command runs under), which silently shadows a same-named one here
        return '{--app= : Filter by app code} {--environment= : Filter by collected environment} {--limit=20 : How many rows to show}';
    }

    /**
     * List the most recent collected DevCloud exceptions for the active team.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        $params = [
            'limit' => (int) $cfCommand->option('limit'),
        ];

        $appCode = $cfCommand->option('app');
        if (!empty($appCode)) {
            $params['appCode'] = $appCode;
        }

        $environment = $cfCommand->option('environment');
        if (!empty($environment)) {
            $params['environment'] = $environment;
        }

        try {
            $exceptions = CDevSuite::devCloudApi()->request('collector/getExceptionList', $params);
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        if (empty($exceptions)) {
            CDevSuite::info('No exception found for the active team.');

            return;
        }

        $rows = [];
        foreach ($exceptions as $exception) {
            $rows[] = [
                carr::get($exception, 'id'),
                carr::get($exception, 'appCode'),
                carr::get($exception, 'occurred'),
                $this->shorten((string) carr::get($exception, 'message'), 60),
                $this->shorten(basename((string) carr::get($exception, 'file')), 30) . ':' . carr::get($exception, 'line'),
                carr::get($exception, 'triggerAt'),
            ];
        }

        $cfCommand->table(['ID', 'App', 'Occ', 'Message', 'File', 'Trigger At'], $rows);
        $cfCommand->line('Detail: <info>phpcf devcloud:exception {id}</info>');
    }

    /**
     * @param string $value
     * @param int    $length
     *
     * @return string
     */
    protected function shorten($value, $length) {
        if (strlen($value) <= $length) {
            return $value;
        }

        return substr($value, 0, $length - 1) . '…';
    }
}
