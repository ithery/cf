<?php

/**
 * Create a new devcloud app under an existing project, wired to an existing
 * git. `appCode`/`appName` default from the current working directory when
 * run from inside `application/{app}/`, matching `phpcf init`'s own
 * `resolveAppCode()`.
 */
class CDevSuite_Command_DevCloud_App_Create extends CDevSuite_CommandAbstract {
    /**
     * @return string
     */
    public function getSignatureArguments() {
        return '{projectId} {gitId} {--appCode=} {--appName=} {--type=} {--uniqid=}';
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

        $appName = $cfCommand->option('appName') ?: ucfirst($appCode);

        try {
            $data = CDevSuite::devCloudApi()->request('app/create', [
                'projectId' => $cfCommand->argument('projectId'),
                'gitId' => $cfCommand->argument('gitId'),
                'appCode' => $appCode,
                'appName' => $appName,
                'appType' => $cfCommand->option('type'),
                'appUniqid' => $cfCommand->option('uniqid'),
            ], 'post');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        CDevSuite::success('App created: ' . carr::get($data, 'appName') . ' (' . carr::get($data, 'appCode') . ')');
        $cfCommand->line('App ID: ' . carr::get($data, 'appId') . ', App Uniqid: ' . carr::get($data, 'appUniqid'));
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
