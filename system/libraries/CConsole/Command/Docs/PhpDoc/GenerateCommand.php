<?php

use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class CConsole_Command_Docs_PhpDoc_GenerateCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:phpdoc:generate {--output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate source documentation by phpdocumentor';

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
            $outputDir = rtrim(CF::appDir(), DS) . DS . 'default' . DS . 'docs/phpdoc/';
        }

        if (!$this->isPhpDocumentorInstalled()) {
            throw new RuntimeException('phpDocumentor is not installed, please install with docs:install command');
        }

        chdir($isFramework ? DOCROOT : c::appRoot());

        if (!CFile::isDirectory($outputDir)) {
            CFile::makeDirectory($outputDir, 0755, true);
        }

        $command = [$this->phpBinary(),
            $this->getPhpDocumentorPhar(), 'run',
            '-d', $inputDir,
            '-t', $outputDir,
            '-c', $this->getPhpDocumentorConfiguration(),
            //'-s', 'template.color=red',
        ];
        //$command = [$this->phpBinary(), $this->getPhpDocumentorPhar(), 'run', '-h'];
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
                $errMessage = 'Something went wrong on running phpDocumentor, please manually check the command';
            }
            $this->error($errMessage);
        }

        return $process->getExitCode();
    }

    private function isPhpDocumentorInstalled() {
        return CDocs::phpDocumentor()->isInstalled();
    }

    private function getPhpDocumentorPhar() {
        return CDocs::phpDocumentor()->phpDocumentorPhar();
    }

    private function getPhpDocumentorConfiguration() {
        return CDocs::phpDocumentor()->phpDocumentorConfiguration();
    }

    private function phpBinary() {
        return (new PhpExecutableFinder())->find(false);
    }
}
