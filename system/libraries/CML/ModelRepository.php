<?php

class CML_ModelRepository {
    protected $basePath;

    public function __construct($basePath = null) {
        if ($basePath == null) {
            $basePath = CF::config('ml.ai_model_path_output');
        }
        $this->basePath = $basePath;
    }

    public function listModels() {
        return CFile::allFiles($this->basePath);
    }

    public function file($file) {
        return $this->basePath . $file;
    }
}
