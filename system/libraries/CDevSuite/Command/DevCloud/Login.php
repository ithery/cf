<?php

/**
 * Description of Login.
 */
class CDevSuite_Command_DevCloud_Login extends CDevSuite_CommandAbstract {
    /**
     * Prompt for DevCloud credentials and cache the resulting OAuth token.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        $username = $cfCommand->ask('Username:');
        $password = $cfCommand->secret('Password:');

        try {
            CDevSuite::devCloudApi()->login($username, $password);
        } catch (Exception $e) {
            CDevSuite::error('Login failed: ' . $e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        CDevSuite::success('Logged in to DevCloud as ' . $username . '. Token stored at ' . CDevSuite::devCloudApi()->tokenPath());
    }
}
