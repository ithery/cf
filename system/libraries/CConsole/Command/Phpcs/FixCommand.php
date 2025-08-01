<?php

/**
 * Description of PhpStanCommand.
 *
 * @author Hery
 */
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class CConsole_Command_Phpcs_FixCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phpcs:fix {file?} {--format=table : format to display} {--debug} {--no-progress}';

    public function handle() {
        $isFramework = CF::appCode() == null;
        $format = $this->option('format');
        $debug = $this->option('debug');
        $noProgress = $this->option('no-progress');
        $appDir = $isFramework ? DOCROOT . 'system/libraries/CElement' : c::appRoot();
        $file = $this->argument('file');
        $scanPath = $appDir;
        if ($file) {
            $scanPath = $file;
        }

        if (!$this->isPhpCbfInstalled()) {
            throw new RuntimeException('phpcs is not installed, please install with phpcs:install command');
        }

        chdir($isFramework ? DOCROOT : c::appRoot());
        //$command = [$this->phpBinary(), $this->getPhpCsPhar(),$appDir];
        $command = [$this->phpBinary(), $this->getPhpCbfPhar(), '--standard='.CQC::phpcs()->phpcsConfiguration()];
        $command[] = $scanPath;
        $process = Process::fromShellCommandline($command, c::appRoot());
        $process->setTimeout(60 * 60);
        $process->start(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        $process->wait();
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            $errMessage = $process->getErrorOutput();
            if (strlen($errMessage) == 0) {
                $errMessage = 'Something went wrong on running phpcs, please manually check the command';
            }
            $this->error($errMessage);
        }

        return $process->getExitCode();
    }

    private function isPhpCbfInstalled() {
        return CQC::phpcs()->isInstalled();
    }

    private function getPhpCbfPhar() {
        return CQC::phpcs()->phpcbfPhar();
    }

    private function phpBinary() {
        return (new PhpExecutableFinder())->find(false);
    }
}
