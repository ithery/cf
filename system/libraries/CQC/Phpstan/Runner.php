<?php

class CQC_Phpstan_Runner {
    protected $directory;

    public function __construct($directory = null) {
        $this->directory = $directory ?: $this->getDefaultDirectory();
        if (!CFile::isDirectory($this->directory)) {
            CFile::makeDirectory($this->directory, 0755, true);
        }
        if (!CFile::isDirectory($this->directory)) {
            throw new Exception('qc phpstan failed, please check directory permission at:' . $this->directory);
        }
    }

    private function runFile() {
        return $this->directory . 'phpstan.run';
    }

    private function doneFile() {
        return $this->directory . 'phpstan.done';
    }

    public function isRunning() {
        return CFile::exists($this->runFile());
    }

    private function startRun() {
        CFile::put($this->runFile(), '');

        return $this->isRunning();
    }

    private function storeRun($output) {
        CFile::append($this->runFile(), $output);
    }

    public function getDoneContent() {
        if (!CFile::isFile($this->doneFile())) {
            return '';
        }

        return CFile::get($this->doneFile());
    }

    public function getRunContent() {
        return CFile::get($this->runFile());
    }

    private function endRun() {
        CFile::move($this->runFile(), $this->doneFile());

        return CFile::exists($this->doneFile()) && (!CFile::exists($this->runFile()));
    }

    public function getDefaultDirectory() {
        return DOCROOT . 'temp' . DS . 'qc' . DS . CF::appCode() . DS . 'phpstan' . DS;
    }

    public function run() {
        if ($this->isRunning()) {
            //throw new Exception('qc phpstan runner is running');
        }
        if (!$this->startRun()) {
            throw new Exception('qc phpstan runner failed running, please check directory permission at:' . $this->directory);
        }

        // $command = ['phpcf', 'phpstan', '--format=json'];
        // $process = new \Symfony\Component\Process\Process($command, c::appRoot());
        // $process->start();
        $cfCli = CConsole::kernel()->cfCli();
        $exitCode = $cfCli->call('phpstan --format=json --no-progress');
        $lines = $cfCli->output();
        $this->storeRun($lines);
        if (!$this->endRun()) {
            throw new Exception('qc phpstan runner failed to end, please check directory permission at:' . $this->directory);
        }
    }

    public function getData() {
        $content = $this->getDoneContent();
        $progress = CFile::exists($this->doneFile()) ? 100 : 0;
        // if ($this->isRunning()) {
        //     $content = $this->getRunContent();
        // }

        return [
            'isRunning' => $this->isRunning(),
            'progress' => $progress,
            'result' => $this->parseContent($content),
        ];
    }

    private function parseContent($content) {
        $parser = new CQC_Phpstan_Runner_ResultParser();

        return $parser->parse($content);
    }
}
