<?php

/**
 * Orchestrates project + git + app + dev database creation in one call -
 * the CLI equivalent of the manual "create project, register git, create
 * app, create+seed a dev database" sequence devcloud otherwise requires
 * clicking through one page at a time.
 *
 * Every step also exists as its own command (`devcloud:project:create`,
 * `devcloud:git:create`, `devcloud:app:create`, `devcloud:database:create-dev`)
 * - this one calls the same API methods directly so it can chain the ids
 * returned by each step into the next, and accepts an existing
 * project/git id to skip creating either from scratch.
 */
class CDevSuite_Command_DevCloud_App_Scaffold extends CDevSuite_CommandAbstract {
    /**
     * @return string
     */
    public function getSignatureArguments() {
        return '{--projectId=} {--projectName=} {--prefix=}'
            . ' {--gitId=} {--repoUrlSsh=} {--repoUrlHttp=} {--gitProviderId=} {--skip-webhook}'
            . ' {--appCode=} {--appName=} {--appType=}'
            . ' {--skip-database} {--dbConnectionId=} {--database=} {--dbName=} {--environment=} {--schema=}';
    }

    /**
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        $appCode = $cfCommand->option('appCode') ?: $this->resolveAppCodeFromCwd();
        if (empty($appCode)) {
            CDevSuite::error('Pass --appCode, or run this from inside an application/{app} folder.');

            return CConsole::FAILURE_EXIT;
        }

        $api = CDevSuite::devCloudApi();

        try {
            $projectId = $cfCommand->option('projectId');
            if (empty($projectId)) {
                $projectName = $cfCommand->option('projectName') ?: ucfirst($appCode);
                $prefix = $cfCommand->option('prefix') ?: cstr::toupper(substr($appCode, 0, 2));
                $cfCommand->line('Creating project ' . $projectName . ' (prefix ' . $prefix . ')...');
                $project = $api->request('project/create', ['name' => $projectName, 'prefix' => $prefix], 'post');
                $projectId = carr::get($project, 'projectId');
            } else {
                $cfCommand->line('Using existing project ' . $projectId);
            }

            $gitId = $cfCommand->option('gitId');
            if (empty($gitId)) {
                $repoUrlSsh = $cfCommand->option('repoUrlSsh');
                if (empty($repoUrlSsh)) {
                    CDevSuite::error('Pass --gitId to reuse an existing git, or --repoUrlSsh to register a new one.');

                    return CConsole::FAILURE_EXIT;
                }
                $cfCommand->line('Registering git ' . $appCode . '...');
                $git = $api->request('git/create', [
                    'name' => $appCode,
                    'repoUrlSsh' => $repoUrlSsh,
                    'repoUrlHttp' => $cfCommand->option('repoUrlHttp'),
                    'gitProviderId' => $cfCommand->option('gitProviderId'),
                    'installWebhook' => !$cfCommand->option('skip-webhook'),
                ], 'post');
                $gitId = carr::get($git, 'gitId');
                if (carr::get($git, 'webhookMessage')) {
                    $cfCommand->warn('Webhook not installed: ' . carr::get($git, 'webhookMessage'));
                }
            } else {
                $cfCommand->line('Using existing git ' . $gitId);
            }

            $appName = $cfCommand->option('appName') ?: ucfirst($appCode);
            $cfCommand->line('Creating app ' . $appCode . '...');
            $app = $api->request('app/create', [
                'projectId' => $projectId,
                'gitId' => $gitId,
                'appCode' => $appCode,
                'appName' => $appName,
                'appType' => $cfCommand->option('appType'),
            ], 'post');

            $dbAccountId = null;
            $database = null;
            if (!$cfCommand->option('skip-database')) {
                $dbConnectionId = $cfCommand->option('dbConnectionId');
                if (empty($dbConnectionId)) {
                    CDevSuite::error('Pass --dbConnectionId, or --skip-database to skip creating a database.');

                    return CConsole::FAILURE_EXIT;
                }
                $database = $cfCommand->option('database') ?: 'appittro_' . $appCode;
                $cfCommand->line('Creating database ' . $database . '...');
                $dbAccount = $api->request('database/createDev', [
                    'dbConnectionId' => $dbConnectionId,
                    'database' => $database,
                    'name' => $cfCommand->option('dbName'),
                    'environment' => $cfCommand->option('environment'),
                    'schema' => $cfCommand->option('schema'),
                ], 'post');
                $dbAccountId = carr::get($dbAccount, 'dbAccountId');
            }
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        CDevSuite::success('Scaffolded ' . $appCode);
        $cfCommand->table(['Field', 'Value'], array_filter([
            ['Project ID', $projectId],
            ['Git ID', $gitId],
            ['App ID', carr::get($app, 'appId')],
            ['App Uniqid', carr::get($app, 'appUniqid')],
            $database ? ['Database', $database] : null,
            $dbAccountId ? ['Db Account ID', $dbAccountId] : null,
        ]));
    }

    /**
     * @return null|string
     */
    protected function resolveAppCodeFromCwd() {
        $appsRoot = c::fixPath(DOCROOT . 'application');
        $cwd = c::fixPath(getcwd());

        if (!cstr::startsWith($cwd, $appsRoot)) {
            return null;
        }

        $relative = trim(substr($cwd, strlen($appsRoot)), DS);
        if (strlen($relative) == 0) {
            return null;
        }

        $segments = explode(DS, $relative);

        return $segments[0];
    }
}
