<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CFConsole {

    private static $commands = [];
    private static $defaultCommands = [
        CConsole_Command_Domain_DomainListCommand::class,
        CConsole_Command_Domain_DomainCreateCommand::class,
        CConsole_Command_Domain_DomainDeleteCommand::class,
        CConsole_Command_Domain_DomainSwitchCommand::class,
        CConsole_Command_Daemon_DaemonListCommand::class,
        CConsole_Command_Daemon_DaemonStartCommand::class,
        CConsole_Command_Daemon_DaemonStatusCommand::class,
        CConsole_Command_Daemon_DaemonStopCommand::class,
        CConsole_Command_StatusCommand::class,
    ];

    public static function execute() {

        $kernel = new CConsole_Kernel();

        $commands = array_merge(static::$defaultCommands, static::$commands);
        CConsole_Application::starting(function ($cfCli) use ($commands) {
            $cfCli->resolveCommands($commands);
        });

        $status = $kernel->handle(
                $input = new Symfony\Component\Console\Input\ArgvInput, new Symfony\Component\Console\Output\ConsoleOutput
        );

        $kernel->terminate($input, $status);


        exit($status);
    }

    public static function addCommand($classArray) {
        $classArray = carr::wrap($classArray);
        foreach ($classArray as $class) {
            if (!class_exists($class)) {
                throw new Exception('Class ' . $class . ' not exists');
            }

            static::$commands[] = $class;
        }
    }

}
