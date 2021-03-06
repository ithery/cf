<?php

/**
 * @method bool     get(string $filename)
 * @method resource readStream(string $filename)
 * @method bool     delete(string $filename)
 * @method bool     exists(string $filename)
 */
class CExporter_Disk {
    /**
     * @var CStorage_Adapter
     */
    protected $disk;

    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var array
     */
    protected $diskOptions;

    /**
     * @param CStorage_Adapter $disk
     * @param null|string      $name
     * @param array            $diskOptions
     */
    public function __construct(CStorage_Adapter $disk, $name = null, array $diskOptions = []) {
        $this->disk = $disk;
        $this->name = $name;
        $this->diskOptions = $diskOptions;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments) {
        return $this->disk->{$name}(...$arguments);
    }

    /**
     * @param string          $destination
     * @param string|resource $contents
     *
     * @return bool
     */
    public function put($destination, $contents) {
        return $this->disk->put($destination, $contents, $this->diskOptions);
    }

    /**
     * @param TemporaryFile $source
     * @param string        $destination
     *
     * @return bool
     */
    public function copy(CExporter_File_TemporaryFile $source, $destination) {
        $readStream = $source->readStream();

        if (realpath($destination)) {
            $tempStream = fopen($destination, 'rb+');
            $success = stream_copy_to_stream($readStream, $tempStream) !== false;

            if (is_resource($tempStream)) {
                fclose($tempStream);
            }
        } else {
            $success = $this->put($destination, $readStream);
        }

        if (is_resource($readStream)) {
            fclose($readStream);
        }

        return $success;
    }

    /**
     * @param TemporaryFile $source
     * @param string        $destination
     *
     * @return bool
     */
    public function copyRaw(CExporter_File_TemporaryFile $source, $destination) {
        $success = $this->put($destination, $source->contents());

        return $success;
    }

    /**
     * @param string $filename
     */
    public function touch($filename) {
        $this->disk->put($filename, '', $this->diskOptions);
    }
}
