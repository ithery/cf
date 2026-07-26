<?php

/**
 * Description of CreateCommand
 */
class CDevSuite_Command_Ssh_CreateCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{name}';
    }

    /**
     * Interactively prompt for connection details and create a new SSH server configuration.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return int|void Exit code when the private key file cannot be found.
     */
    public function run(CConsole_Command $cfCommand) {
        $name = $cfCommand->argument('name');

        if (CDevSuite::ssh()->exists($name)) {
            CDevSuite::error('Ssh configuration: ' . $name . ' already exists');
            exit(CConsole::FAILURE_EXIT);
        }
        $data = [];

        $host = $cfCommand->ask('Host:', 'localhost');
        $defaultPort = '22';
        $port = $cfCommand->ask('Port:', $defaultPort);

        $user = $cfCommand->ask('User:', 'root');

        $passwordType = $cfCommand->choice('Password Type:', ['password', 'pubkey'], 1, 2);
        if ($passwordType == 'password') {
            $password = $cfCommand->secret('Password:');
        } else {
            $password = $cfCommand->ask('File Path:', '~/.ssh/id_rsa');
            if (!file_exists($password)) {
                CDevSuite::error($password . ' not found');
                $password = $cfCommand->ask('File Path:', '~/.ssh/id_rsa');
                if (!file_exists($password)) {
                    CDevSuite::error($password . ' not found, please try again');
                    return CConsole::FAILURE_EXIT;
                }
            }
        }

        if ($passwordType != 'password') {
            $password = realpath($password);
        }
        $data = [
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'password' => $password,
            'passwordType' => $passwordType,
        ];

        if (CDevSuite::ssh()->create($name, $data)) {
            CDevSuite::success('A [' . $name . '] database configuration has been created');
        }
    }
}
