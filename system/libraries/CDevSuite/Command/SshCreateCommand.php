<?php

/**
 * Description of ServerCreateCommand
 *
 * @author Hery
 */
class CDevSuite_Command_SshCreateCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '{name}';
    }

    public function run(CConsole_Command $cfCommand) {
        $name = $cfCommand->argument('name');

        if (CDevSuite::ssh()->exists($name)) {
            CDevSuite::error('Ssh configuration: ' . $key . ' already exists');
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
            if(!file_exists($password)) {
                CDevSuite::error($password.' not found');
                $password = $cfCommand->ask('File Path:', '~/.ssh/id_rsa');
                if(!file_exists($password)){
                    CDevSuite::error($password.' not found, please try again');
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
