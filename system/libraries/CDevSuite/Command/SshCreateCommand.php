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

        if (CDevSuite::ssh()->exists($key)) {
            CDevSuite::error('Databaes configuration: ' . $key . ' already exists');
            exit(CConsole::FAILURE_EXIT);
        }
        $name = $cfCommand->argument('name');
        $data = [];

        $host = $cfCommand->ask('Host:', 'localhost');
        $defaultPort = '22';
        $port = $cfCommand->ask('Port:', $defaultPort);

        $user = $cfCommand->ask('User:', 'root');

        $blankPassword = '';
        if ($type == 'mysql') {
            $blankPassword = '[blank]';
        }
        $password = $cfCommand->ask('Password:', $blankPassword);
        if ($password == $blankPassword) {
            $password = '';
        }
        $data = [
            'type' => $type,
            'host' => $host,
            'database' => $database,
            'port' => $port,
            'user' => $user,
            'password' => $password,
        ];

        if (CDevSuite::db()->create($name, $data)) {
            CDevSuite::success('A [' . $name . '] database configuration has been created');
        }
    }

}
