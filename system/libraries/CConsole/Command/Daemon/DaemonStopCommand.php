<?php

/**
 * Description of DaemonStopCommand
 *
 * @author Hery
 */
class CConsole_Command_Daemon_DaemonStopCommand extends CConsole_Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daemon:stop {class}';

    public function handle() {
        $class = $this->argument('class');

        $errCode = 0;
        $errMessage = '';
        $daemonManager = CManager::daemon();
        if (!$daemonManager->isRunning($class)) {
            $errCode++;
            $errMessage = $class . ' already stopped';
        }
        if ($errCode == 0) {
            $this->info('Stopping ' . $class);
            try {

                $started = $daemonManager->stop($class);

                $this->info('Daemon ' . $class . ' is stopped now');
            } catch (Exception $ex) {
                $errCode++;
                $errMessage = $ex->getMessage();
            }
        }


        if ($errCode > 0) {
            $this->error($errMessage);
            return 1;
        }

        return 0;
    }

}
