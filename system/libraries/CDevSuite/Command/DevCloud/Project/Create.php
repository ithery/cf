<?php

/**
 * Create a new devcloud project under the authenticated user's current team.
 */
class CDevSuite_Command_DevCloud_Project_Create extends CDevSuite_CommandAbstract {
    /**
     * @return string
     */
    public function getSignatureArguments() {
        return '{name} {prefix}';
    }

    /**
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        $name = $cfCommand->argument('name');
        $prefix = $cfCommand->argument('prefix');

        try {
            $data = CDevSuite::devCloudApi()->request('project/create', [
                'name' => $name,
                'prefix' => $prefix,
            ], 'post');
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        CDevSuite::success('Project created: ' . carr::get($data, 'name') . ' (prefix ' . carr::get($data, 'prefix') . ')');
        $cfCommand->line('Project ID: ' . carr::get($data, 'projectId'));
    }
}
