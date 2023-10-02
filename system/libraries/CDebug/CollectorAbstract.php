<?php

abstract class CDebug_CollectorAbstract {
    public function put($data) {
        $type = $this->getType();
        if (!in_array($type, CDebug_CollectorManager::allCollectorType())) {
            throw new Exception("Type ${type} is not found");
        }

        if (!is_string($data)) {
            $data = json_encode($data);
        }

        json_decode($data);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(json_last_error_msg());
        }

        $path = $this->getDirectory();
        $path .= $type . DS;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $path .= date('YmdH') . $this->getExt();

        file_put_contents($path, $data . PHP_EOL, FILE_APPEND | LOCK_EX);

        return true;
    }

    protected function getDirectory() {
        $path = DOCROOT . 'temp' . DS;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $path .= 'collector' . DS;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    abstract public function getType();

    public function getExt() {
        return '.txt';
    }
}
