<?php

class CServer_NodeJs {
    protected $modulePaths = [];

    protected $maxInstallRetry = 3;

    protected $nodePath;

    public function __construct($nodePath = null) {
        $this->nodePath = $nodePath ?: 'node';
    }

    public function setModulePath($module, $path) {
        $this->modulePaths[$module] = $path;
    }

    public function setMaxInstallRetry($count) {
        $this->maxInstallRetry = $count;
    }

    /**
     * @return CServer_NodeJs_Runner
     */
    public function createRunner() {
        $runner = new CServer_NodeJs_Runner($this->nodePath);
        $runner->setModulePaths($this->modulePaths);
        $runner->setMaxInstallRetry($this->maxInstallRetry);

        return $runner;
    }

    /**
     * Create React Wrapper.
     *
     * @param string $reactScript
     * @param bool   $sourceMap
     *
     * @return CServer_NodeJs_Wrapper_ReactWrapper
     */
    public function createReact($reactScript, $sourceMap = false) {
        $react = new CServer_NodeJs_Wrapper_ReactWrapper($this->createRunner(), $reactScript, $sourceMap);

        return $react;
    }
}
