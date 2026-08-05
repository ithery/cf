<?php

/**
 * Register an existing git repository under the authenticated user's
 * current team. Does not create anything on GitHub/GitLab itself.
 */
class CDevSuite_Command_DevCloud_Git_Create extends CDevSuite_CommandAbstract {
    /**
     * @return string
     */
    public function getSignatureArguments() {
        return '{name} {repoUrlSsh} {--repoUrlHttp=} {--gitProviderId=} {--skip-webhook}';
    }

    /**
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        $name = $cfCommand->argument('name');
        $repoUrlSsh = $cfCommand->argument('repoUrlSsh');

        try {
            $data = CDevSuite::devCloudApi()->request('git/create', [
                'name' => $name,
                'repoUrlSsh' => $repoUrlSsh,
                'repoUrlHttp' => $cfCommand->option('repoUrlHttp'),
                'gitProviderId' => $cfCommand->option('gitProviderId'),
                'installWebhook' => !$cfCommand->option('skip-webhook'),
            ], 'post');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        CDevSuite::success('Git registered: ' . carr::get($data, 'name'));
        $cfCommand->line('Git ID: ' . carr::get($data, 'gitId'));

        if (carr::get($data, 'webhookInstalled')) {
            $cfCommand->line('Push webhook installed.');
        } elseif (carr::get($data, 'webhookMessage')) {
            $cfCommand->warn('Webhook not installed: ' . carr::get($data, 'webhookMessage'));
        }
    }
}
