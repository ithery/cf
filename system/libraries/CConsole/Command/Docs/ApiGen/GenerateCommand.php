<?php

use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class CConsole_Command_Docs_ApiGen_GenerateCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:apigen:generate {--output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate source documentation by apigen';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $isFramework = CF::appCode() == null;
        $outputDir = $this->option('output');
        $inputDir = CF::appDir();
        if ($outputDir == null) {
            $outputDir = rtrim(CF::appDir(), DS) . DS . 'default' . DS . 'docs/apigen/';
        }

        if (!$this->isApiGenInstalled()) {
            throw new RuntimeException('apigen is not installed, please install with docs:install command');
        }

        chdir($isFramework ? DOCROOT : c::appRoot());

        if (!CFile::isDirectory($outputDir)) {
            CFile::makeDirectory($outputDir, 0755, true);
        }

        $command = [$this->phpBinary(),
            '-d', 'error_reporting=-1',
            $this->getApiGenPhar(),
            $inputDir,
            '--output', $outputDir,
            '--config', $this->getApiGenConfiguration(),
        ];
        //$command = [$this->phpBinary(), $this->getApiGenPhar(), 'run', '-h'];
        $process = Process::fromShellCommandline($command, c::appRoot());
        cdbg::dd($process->getCommandLine());
        $process->setTimeout(60 * 60);
        $process->start(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        $process->wait();
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            $errMessage = $process->getErrorOutput();
            if (strlen($errMessage) == 0) {
                $errMessage = 'Something went wrong on running apiGen, please manually check the command';
            }
            $this->error($errMessage);
        }

        return $process->getExitCode();
    }

    private function isApiGenInstalled() {
        return CDocs::apiGen()->isInstalled();
    }

    private function getApiGenPhar() {
        return CDocs::apiGen()->apiGenPhar();
    }

    private function getApiGenConfiguration() {
        return CDocs::apiGen()->apiGenConfiguration();
    }

    private function phpBinary() {
        return (new PhpExecutableFinder())->find(false);
    }
}
