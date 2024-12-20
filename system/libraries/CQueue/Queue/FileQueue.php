<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CQueue_Queue_FileQueue extends CQueue_AbstractQueue {
    /**
     * The File Directory.
     *
     * @var string
     */
    protected $path;

    /**
     * The name of the default queue.
     *
     * @var string
     */
    protected $default;

    /**
     * Create a new queue instance.
     *
     * @param null|mixed $path
     * @param mixed      $default
     *
     * @return void
     */
    public function __construct($path = null, $default = 'default') {
        $this->path = $path;
        $this->default = $default;
    }

    /**
     * Get the size of the queue.
     *
     * @param null|string $queue
     *
     * @return int
     */
    public function size($queue = null) {
        $line = 0;
        $file = $this->getFile($queue, 'r');
        while (false !== fgets($file)) {
            $line++;
        }

        return $line;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string      $job
     * @param mixed       $data
     * @param null|string $queue
     *
     * @return mixed
     */
    public function push($job, $data = '', $queue = null) {
        return $this->later(0, $job, $data, $queue);
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param string $payload
     * @param string $queue
     * @param array  $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = []) {
        return $this->laterRaw(0, $payload, $queue);
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param string                               $job
     * @param mixed                                $data
     * @param null|string                          $queue
     *
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null) {
        $payload = $this->createPayload($job, $this->getQueue($queue), $data);

        return $this->laterRaw($delay, $payload, $queue);
    }

    /**
     * Push a raw payload onto the queue after a delay.
     *
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param string                               $payload
     * @param null|string                          $queue
     *
     * @return mixed
     */
    public function laterRaw($delay, $payload, $queue = null) {
        $file = $this->getFile($queue, 'a');
        flock($file, LOCK_EX);
        fwrite($file, $this->availableAt($delay) . ' ' . $payload . PHP_EOL);
        flock($file, LOCK_UN);
        fclose($file);

        return carr::get(json_decode($payload, true), 'uuid');
    }

    /**
     * Create a payload string from the given job and data.
     *
     * @param string $job
     * @param mixed  $data
     * @param string $queue
     *
     * @return string
     */
    protected function createPayloadArray($job, $queue, $data = '') {
        return array_merge(parent::createPayloadArray($job, $queue, $data), [
            'uuid' => cstr::uuid(),
            'attempts' => 0,
        ]);
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param null|string $queue
     *
     * @return null|\CQueue_JobInterface
     */
    public function pop($queue = null) {
        $payload = $this->popOrRelease($queue);
        if ($payload) {
            return new CQueue_Job_FileJob($this->container, $this, $payload, $queue);
        }

        return null;
    }

    public function popOrRelease($queue = null, $payload = null, $delay = null) {
        $path = $this->getQueueFilePath($queue);
        if (!file_exists($path)) {
            return null;
        }
        $data = null;
        $line = '';
        $now = time();
        $ptmp = $this->getQueueFilePath("{$queue}.tmp");
        $file = $this->getFile($queue, 'r+');
        $ftmp = $this->getFile("{$queue}.tmp", 'w');
        flock($file, LOCK_EX);
        flock($ftmp, LOCK_EX);
        while (1) {
            $chr = fgetc($file);
            if ($chr === false) {
                break;
            }
            if ($chr === PHP_EOL) {
                $tim = substr($line, 0, 10);
                if (is_numeric($tim)) {
                    $tim = (int) $tim;
                    $line = substr($line, 11);
                } else {
                    $tim = 0;
                }
                $yes = $tim <= $now;
                if ($yes && is_null($data) && is_null($payload)) { // pop
                    $data = trim($line);
                    $line = '';
                } elseif ($line === $payload) {
                    if ($delay === false) { // del
                        $data = true;
                        $line = '';
                    } else { // set
                        $data = true;
                        $tim = $now + (int) $delay;
                    }
                }
                if ($line) {
                    fwrite($ftmp, "{$tim} {$line}" . PHP_EOL);
                    $line = '';
                }
            } else {
                $line .= $chr;
            }
        }
        ftruncate($file, 0);
        flock($file, LOCK_UN);
        flock($ftmp, LOCK_UN);
        fclose($file);
        fclose($ftmp);
        if (isset($data)) {
            @unlink($path);
            @rename($ptmp, $path);
        }

        return $data;
    }

    /**
     * Get the queue or return the default.
     *
     * @param null|string $queue
     *
     * @return string
     */
    public function getQueue($queue) {
        return 'queue-file-' . ($queue ?: $this->default);
    }

    /**
     * Get the File Full Path.
     *
     * @param mixed      $queue
     * @param null|mixed $mode
     *
     * @throws \RuntimeException
     *
     * @return string|resource
     */
    public function getFile($queue, $mode = null) {
        $dir = $this->getPath();
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $path = $dir . $this->getQueue($queue);
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
                throw new \RuntimeException("Unable to open file for queue: {$queue}, path: {$path}");
            }
        } else {
            $file = $path;
        }

        return $file;
    }

    /**
     * Get the File Path.
     *
     * @param null|mixed $queue
     *
     * @return string
     */
    public function getQueueFilePath($queue = null) {
        return $this->getPath($this->getQueue($queue));
    }

    /**
     * Get the File Path.
     *
     * @param mixed $file
     *
     * @return string
     */
    public function getPath($file = '') {
        $path = $this->path;
        $path = rtrim($path, '/\\') . '/' . ltrim($file, '/\\');

        return $path;
    }
}
