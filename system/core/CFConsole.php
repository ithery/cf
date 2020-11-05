<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CFConsole {

    public static function execute() {

        $kernel = new CConsole_Kernel();

        $commands = [
            CConsole_Command_DomainCommand::class,
            CConsole_Command_StatusCommand::class,
            CConsole_Command_QC_PhpUnitCommand::class,
            CConsole_Command_QC_PhpUnitListCommand::class,
        ];
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
