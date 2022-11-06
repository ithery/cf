<?php

abstract class CServer_NodeJs_WrapperAbstract implements CServer_NodeJs_Contract_WrapperInterface {
    protected $path;

    protected $node;

    public function __construct(CServer_NodeJs_Runner $node, $file) {
        $this->path = $file;
        $this->node = $node;
    }

    public function execModuleScript($module, $script, $arguments, $fallback = null) {
        if (is_null($fallback)) {
            $fallback = function () {
            };
        }

        return $this->node->execModuleScript($module, $script, $arguments, $fallback);
    }

    public function getPath($defaultName = 'source.tmp') {
        return $this->path;
    }

    public function getSource() {
        return file_get_contents($this->path);
    }

    public function getResult($outfile = null) {
        $result = $this->compile($outfile);
        if ($result !== false && $result !== null) {
            return $result;
        }

        return $this->fallback();
    }

    public function exec() {
        return $this->getResult();
    }

    public function write($path) {
        return file_put_contents($path, $this->getResult());
    }

    public function __toString() {
        return $this->getResult();
    }
}
