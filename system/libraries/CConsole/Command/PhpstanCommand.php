<?php

/**
 * Description of PhpStanCommand.
 *
 * @author Hery
 */
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class CConsole_Command_PhpstanCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phpstan {file} {--format=table : format to display} {--debug} {--no-progress}';

    public function handle() {
        $format = $this->option('format');
        $debug = $this->option('debug');
        $noProgress = $this->option('no-progress');
        $appDir = c::appRoot();
        $file = $this->argument('file');
        $scanPath = $appDir;
        if ($file) {
            $scanPath = $file;
        }

        if (!$this->isPhpStanInstalled()) {
            throw new RuntimeException('phpstan is not installed, please install with phpstan:install command');
        }
        chdir(c::appRoot());
        $command = [$this->phpBinary(), '-c', CQC::phpstan()->phpstanConfiguration(), '-d', 'memory_limit=1G', '-d', 'max_execution_time=0', $this->getPhpStanBinary(), 'analyze', '--error-format', $format, '--autoload-file', CQC::phpstan()->phpstanBootstrap()];
        if ($debug) {
            $command[] = '--debug';
        }
        if ($noProgress) {
            $command[] = '--no-progress';
        }
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
                $errMessage = 'Something went wrong on running phpstan, please manually check the command';
            }
            $this->error($errMessage);
        }

        return $process->getExitCode();
    }

    private function isPhpStanInstalled() {
        return CQC::phpstan()->isInstalled();
    }

    private function getPhpStanBinary() {
        return CQC::phpstan()->phpstanBinary();
    }

    private function phpBinary() {
        return (new PhpExecutableFinder())->find(false);
    }
}
