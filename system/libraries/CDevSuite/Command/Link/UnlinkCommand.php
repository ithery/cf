<?php

/**
 * Description of UnlinkCommand.
 */
class CDevSuite_Command_Link_UnlinkCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{name?} {--secure}';
    }

    /**
     * Remove the symbolic link for the given (or current directory's) site.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return void
     */
    public function run(CConsole_Command $cfCommand) {
        $name = $cfCommand->argument('name') ?: CF::appCode();
        CDevSuite::info('The [' . CDevSuite::site()->unlink($name) . '] symbolic link has been removed.');
    }
}
