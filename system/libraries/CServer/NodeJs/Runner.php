<?php

class CServer_NodeJs_Runner {
    protected $modulePaths = [];

    protected $maxInstallRetry = 3;

    protected $nodePath;

    public function __construct($nodePath = null) {
        $this->nodePath = $nodePath ?: 'node';
    }

    public function setModulePath($module, $path) {
        $this->modulePaths[$module] = $path;

        return $this;
    }

    public function setModulePaths($modulePaths) {
        $this->modulePaths = $modulePaths;

        return $this;
    }

    public function setMaxInstallRetry($count) {
        $this->maxInstallRetry = $count;

        return $this;
    }

    public function installer() {
        return new CServer_NodeJs_Installer($this->getDirectory(), $this->modulePaths, $this->maxInstallRetry);
    }

    public function exec($script, $fallback = null) {
        return $this->execOrFallback($script, $fallback, false);
    }

    protected function execOrFallback($script, $fallback, $withNode) {
        $exec = $this->checkFallback($fallback)
            ? $this->shellExec($withNode)
            : $fallback;

        return call_user_func($exec, $script);
    }

    protected function checkFallback($fallback) {
        if ($this->isNodeInstalled()) {
            return true;
        }

        if (is_null($fallback)) {
            throw new ErrorException('Please install node.js or provide a PHP fallback.', 2);
        }

        if (!is_callable($fallback)) {
            throw new InvalidArgumentException('The fallback provided is not callable.', 1);
        }

        return false;
    }

    public function isNodeInstalled() {
        $exec = $this->shellExec(true);

        return substr($exec('--version'), 0, 1) === 'v';
    }

    protected function shellExec($withNode) {
        $prefix = $withNode ? $this->getNodePath() . ' ' : '';

        return function ($script) use ($prefix) {
            return shell_exec($prefix . $script . ' 2>&1');
        };
    }

    public function getNodePath() {
        return $this->nodePath;
    }

    public static function getDirectory() {
        $dir = DOCROOT . 'temp' . DS . 'nodejs' . DS . CF::appCode();
        if (!CFile::isDirectory($dir)) {
            CFile::makeDirectory($dir);
        }

        return $dir;
    }

    public function execModuleScript($module, $script, $arguments, $fallback = null) {
        return $this->nodeExec(
            static::getModuleScript($module, $script) . (empty($arguments) ? '' : ' ' . $arguments),
            $fallback
        );
    }

    public function nodeExec($script, $fallback = null) {
        return $this->execOrFallback($script, $fallback, true);
    }

    public function getModuleScript($module, $script) {
        $module = $this->getNodeModule($module);
        $path = $module . DIRECTORY_SEPARATOR . $script;
        if (!file_exists($path)) {
            throw new InvalidArgumentException("The ${script} was not found in the module path ${module}.", 3);
        }

        return escapeshellarg(realpath($path));
    }

    public function getNodeModule($module) {
        return empty($this->modulePaths[$module])
            ? $this->getNodeModules() . DIRECTORY_SEPARATOR . $module
            : $this->modulePaths[$module];
    }

    public function getNodeModules() {
        return $this->getDirectory() . DIRECTORY_SEPARATOR . 'node_modules';
    }
}
