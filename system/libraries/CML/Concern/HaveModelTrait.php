<?php

trait CML_Concern_HaveModelTrait {
    protected $modelFile;

    protected $modelPath;

    /**
     * @param string      $filename
     * @param null|string $path
     *
     * @return static
     */
    public function setModelFile($filename, $path = null) {
        if ($path != null) {
            $this->modelPath = $path;
        }
        $this->modelFile = $filename;

        return $this;
    }

    public function getModelPath() {
        return $this->modelPath;
    }

    public function getModelFile() {
        return $this->modelFile;
    }
}
