<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CFConsole {

    private static $commands = [];

    public static function addCommand($classArray) {
        $classArray = carr::wrap($classArray);
        foreach ($classArray as $class) {
            if (!class_exists($class)) {
                throw new Exception('Class ' . $class . ' not exists');
            }

            static::$commands[] = $class;
        }
    }

    public static function execute() {

        $kernel = new CConsole_Kernel();

        $commands = array_merge([
            CConsole_Command_DomainCommand::class,
            CConsole_Command_StatusCommand::class,
                ], static::$commands);
        CConsole_Application::starting(function ($cfCli) use ($commands) {
            $cfCli->resolveCommands($commands);
        });

        $status = $kernel->handle(
                $input = new Symfony\Component\Console\Input\ArgvInput, new Symfony\Component\Console\Output\ConsoleOutput
        );


        $kernel->terminate($input, $status);


        exit($status);
    }

}
