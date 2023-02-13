<?php

class CQC_Testing_Database_Data {
    protected $path;

    protected $data;

    public function __construct($path) {
        $this->path = $path;
        $this->load();
    }

    protected function load() {
        $data = [];
        if (CFile::exists($this->path)) {
            $data = json_decode(CFile::get($this->path), true);
        }
        $this->data = $data;
    }

    protected function save() {
        $file = $this->getFile('w');
        flock($file, LOCK_EX);
        fwrite($file, json_encode($this->data, JSON_PRETTY_PRINT) . PHP_EOL);
        flock($file, LOCK_UN);
        fclose($file);
    }

    /**
     * @param string      $key
     * @param null|string $value
     *
     * @return mixed
     */
    public function set($key, $value) {
        $this->data[$key] = $value;
        $this->save();
    }

    /**
     * @param string     $key
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null) {
        return carr::get($this->data, $key, $default);
    }

    /**
     * Get the File Full Path.
     *
     * @param null|mixed $mode
     *
     * @throws \RuntimeException
     *
     * @return string|resource
     */
    protected function getFile($mode = null) {
        $path = $this->path;
        if (!file_exists($path)) {
            $file = fopen($path, 'a');
            if (flock($file, LOCK_EX)) {
                fwrite($file, '');
                flock($file, LOCK_UN);
            }
            fclose($file);
        }
        if (isset($mode)) {
            $file = fopen($path, $mode);
            if ($file === false) {
                throw new \RuntimeException("Unable to open file : {$path}");
            }
        } else {
            $file = $path;
        }

        return $file;
    }
}
