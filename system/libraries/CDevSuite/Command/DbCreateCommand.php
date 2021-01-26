<?php

/**
 * Description of DbCreateCommand
 *
 * @author Hery
 */
class CDevSuite_Command_DbCreateCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '{name}';
    }

    public function run(CConsole_Command $cfCommand) {
        $name = $cfCommand->argument('name');
        if (CDevSuite::db()->exists($name)) {
            CDevSuite::error('Database configuration: ' . $key . ' already exists');
            exit(CConsole::FAILURE_EXIT);
        }
        
        $data = [];

        $type = $cfCommand->choice('Type:', ['mysql', 'mongodb'], 0, 1);
        $host = $cfCommand->ask('Host:', 'localhost');
        $defaultPort = '3306';
        if ($type == 'mongodb') {
            $defaultPort = '27017';
        }
        $port = $cfCommand->ask('Port:', $defaultPort);

        $database = $cfCommand->ask('Database:', 'cresenity');
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
            CDevSuite::success('A [' . $name . '] ssh configuration has been created');
        }
    }

}
