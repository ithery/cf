<?php

namespace Pheanstalk;

use Pheanstalk\Contract\SocketFactoryInterface;
use Pheanstalk\Contract\SocketInterface;
use Pheanstalk\Socket\FsockopenSocket;
use Pheanstalk\Socket\SocketSocket;
use Pheanstalk\Socket\StreamSocket;

class SocketFactory implements SocketFactoryInterface {

    const AUTODETECT = 0;
    const STREAM = 1;
    const SOCKET = 2;
    const FSOCKOPEN = 3;

    private $timeout;
    private $host;
    private $port;

    /** @var int */
    private $implementation;

    public function __construct($host, $port, $timeout = 10, $implementation = self::AUTODETECT) {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->setImplementation($implementation);
    }

    public function getImplementation() {
        return $this->implementation;
    }

    public function setImplementation($implementation) {
        if ($implementation === self::AUTODETECT) {
            // Prefer socket
            if (extension_loaded('sockets')) {
                $this->implementation = self::SOCKET;
                return;
            }

            // Then fall back to stream
            if (function_exists('stream_socket_client')) {
                $this->implementation = self::STREAM;
                return;
            }

            // Then fall back to fsockopen
            if (function_exists('fsockopen')) {
                $this->implementation = self::FSOCKOPEN;
            }
        } else {
            $this->implementation = $implementation;
        }
    }

    private function createStreamSocket() {
        return new StreamSocket($this->host, $this->port, $this->timeout);
    }

    private function createSocketSocket() {
        return new SocketSocket($this->host, $this->port, $this->timeout);
    }

    private function createFsockopenSocket() {
        return new FsockopenSocket($this->host, $this->port, $this->timeout);
    }

    /**
     * This function must return a connected socket that is ready for reading / writing.
     * @return SocketInterface
     */
    public function create() {
        switch ($this->implementation) {
            case self::SOCKET:
                return $this->createSocketSocket();
            case self::STREAM:
                return $this->createStreamSocket();
            case self::FSOCKOPEN:
                return $this->createFsockopenSocket();
            default:
                throw new \RuntimeException("Unknown implementation");
        }
    }

}
