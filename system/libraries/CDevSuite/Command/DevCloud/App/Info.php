<?php

/**
 * Description of Info.
 */
class CDevSuite_Command_DevCloud_App_Info extends CDevSuite_CommandAbstract {
    /**
     * Show the DevCloud project/app info for the app in the current
     * working directory (application/{appCode}), if the logged in user
     * belongs to that app's project.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        $appCode = CF::appCode();

        if (empty($appCode)) {
            CDevSuite::error('Run this command from inside an application/{app} folder.');

            return CConsole::FAILURE_EXIT;
        }

        try {
            $data = CDevSuite::devCloudApi()->request('app/getInfo', ['appCode' => $appCode]);
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        $project = carr::get($data, 'project', []);
        $app = carr::get($data, 'app', []);
        $git = carr::get($data, 'git');

        $cfCommand->line('<info>Project</info>');
        $cfCommand->table(['Field', 'Value'], [
            ['Project ID', carr::get($project, 'projectId')],
            ['Prefix', carr::get($project, 'prefix')],
            ['Name', carr::get($project, 'name')],
        ]);

        $cfCommand->line('<info>App</info>');
        $cfCommand->table(['Field', 'Value'], [
            ['App ID', carr::get($app, 'appId')],
            ['Code', carr::get($app, 'appCode')],
            ['Name', carr::get($app, 'appName')],
            ['Type', carr::get($app, 'appType')],
        ]);

        if ($git) {
            $cfCommand->line('<info>Git</info>');
            $cfCommand->table(['Field', 'Value'], [
                ['Clone (SSH)', carr::get($git, 'repositoryCloneSSH')],
                ['Clone (HTTP)', carr::get($git, 'repositoryCloneHTTP')],
            ]);
        }
    }
}
