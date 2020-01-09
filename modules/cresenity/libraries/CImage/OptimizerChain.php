<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

class CImage_OptimizerChain {
    /* @var \CImage_OptimizerAbstract[] */

    protected $optimizers = [];

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var int */
    protected $timeout = 60;

    public function __construct() {

        $this->useLogger(new CImage_Logger_NullLogger());
    }

    public function getOptimizers() {
        return $this->optimizers;
    }

    public function addOptimizer(CImage_OptimizerAbstract $optimizer) {
        $this->optimizers[] = $optimizer;
        return $this;
    }

    public function setOptimizers(array $optimizers) {
        $this->optimizers = [];
        foreach ($optimizers as $optimizer) {
            $this->addOptimizer($optimizer);
        }
        return $this;
    }

    /*
     * Set the amount of seconds each separate optimizer may use.
     */

    public function setTimeout($timeoutInSeconds) {
        $this->timeout = $timeoutInSeconds;
        return $this;
    }

    public function useLogger(LoggerInterface $log) {
        $this->logger = $log;
        return $this;
    }

    public function optimize($pathToImage, $pathToOutput = null) {
        if ($pathToOutput) {
            copy($pathToImage, $pathToOutput);
            $pathToImage = $pathToOutput;
        }
        $image = new CImage_Image($pathToImage);
        $this->logger->info("Start optimizing {$pathToImage}");
        foreach ($this->optimizers as $optimizer) {
            $this->applyOptimizer($optimizer, $image);
        }
    }

    protected function applyOptimizer(CImage_OptimizerAbstract $optimizer, CImage_Image $image) {
        if (!$optimizer->canHandle($image)) {
            return;
        }
        $optimizerClass = get_class($optimizer);
        $this->logger->info("Using optimizer: `{$optimizerClass}`");
        $optimizer->setImagePath($image->path());
        $command = $optimizer->getCommand();
        $this->logger->info("Executing `{$command}`");
        $process = Process::fromShellCommandline($command);
        $process
                ->setTimeout($this->timeout)
                ->run();
        $this->logResult($process);
    }

    protected function logResult(Process $process) {
        if (!$process->isSuccessful()) {
            $this->logger->error("Process errored with `{$process->getErrorOutput()}`");
            return;
        }
        $this->logger->info("Process successfully ended with output `{$process->getOutput()}`");
    }

}
