<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 16, 2019, 5:30:27 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * TcpConnection.
 */
class CDaemon_Listener_Connection_TcpConnection extends CDaemon_Listener_ConnectionAbstract {

    /**
     * Read buffer size.
     *
     * @var int
     */
    const READ_BUFFER_SIZE = 65535;

    /**
     * Status initial.
     *
     * @var int
     */
    const STATUS_INITIAL = 0;

    /**
     * Status connecting.
     *
     * @var int
     */
    const STATUS_CONNECTING = 1;

    /**
     * Status connection established.
     *
     * @var int
     */
    const STATUS_ESTABLISHED = 2;

    /**
     * Status closing.
     *
     * @var int
     */
    const STATUS_CLOSING = 4;

    /**
     * Status closed.
     *
     * @var int
     */
    const STATUS_CLOSED = 8;

    /**
     * Emitted when data is received.
     *
     * @var callback
     */
    public $onMessage = null;

    /**
     * Emitted when the other end of the socket sends a FIN packet.
     *
     * @var callback
     */
    public $onClose = null;

    /**
     * Emitted when an error occurs with connection.
     *
     * @var callback
     */
    public $onError = null;

    /**
     * Emitted when the send buffer becomes full.
     *
     * @var callback
     */
    public $onBufferFull = null;

    /**
     * Emitted when the send buffer becomes empty.
     *
     * @var callback
     */
    public $onBufferDrain = null;

    /**
     * Application layer protocol.
     * The format is like this Workerman\\Protocols\\Http.
     *
     * @var \Workerman\Protocols\ProtocolInterface
     */
    public $protocol = null;

    /**
     * Transport (tcp/udp/unix/ssl).
     *
     * @var string
     */
    public $transport = 'tcp';

    /**
     * Which worker belong to.
     *
     * @var Worker
     */
    public $worker = null;

    /**
     * Bytes read.
     *
     * @var int
     */
    public $bytesRead = 0;

    /**
     * Bytes written.
     *
     * @var int
     */
    public $bytesWritten = 0;

    /**
     * Connection->id.
     *
     * @var int
     */
    public $id = 0;

    /**
     * A copy of $worker->id which used to clean up the connection in worker->connections
     *
     * @var int
     */
    protected $_id = 0;

    /**
     * Sets the maximum send buffer size for the current connection.
     * OnBufferFull callback will be emited When the send buffer is full.
     *
     * @var int
     */
    public $maxSendBufferSize = 1048576;

    /**
     * Default send buffer size.
     *
     * @var int
     */
    public static $defaultMaxSendBufferSize = 1048576;

    /**
     * Sets the maximum acceptable packet size for the current connection.
     *
     * @var int
     */
    public $maxPackageSize = 1048576;

    /**
     * Default maximum acceptable packet size.
     *
     * @var int
     */
    public static $defaultMaxPackageSize = 10485760;

    /**
     * Id recorder.
     *
     * @var int
     */
    protected static $idRecorder = 1;

    /**
     * Socket
     *
     * @var resource
     */
    protected $socket = null;

    /**
     * Send buffer.
     *
     * @var string
     */
    protected $sendBuffer = '';

    /**
     * Receive buffer.
     *
     * @var string
     */
    protected $recvBuffer = '';

    /**
     * Current package length.
     *
     * @var int
     */
    protected $_currentPackageLength = 0;

    /**
     * Connection status.
     *
     * @var int
     */
    protected $status = self::STATUS_ESTABLISHED;

    /**
     * Remote address.
     *
     * @var string
     */
    protected $remoteAddress = '';

    /**
     * Is paused.
     *
     * @var bool
     */
    protected $isPaused = false;

    /**
     * SSL handshake completed or not.
     *
     * @var bool
     */
    protected $sslHandshakeCompleted = false;

    /**
     * All connection instances.
     *
     * @var array
     */
    public static $connections = array();

    /**
     * Status to string.
     *
     * @var array
     */
    public static $statusToString = array(
        self::STATUS_INITIAL => 'INITIAL',
        self::STATUS_CONNECTING => 'CONNECTING',
        self::STATUS_ESTABLISHED => 'ESTABLISHED',
        self::STATUS_CLOSING => 'CLOSING',
        self::STATUS_CLOSED => 'CLOSED',
    );

    /**
     * Adding support of custom functions within protocols
     *
     * @param string $name
     * @param array  $arguments
     * @return void
     */
    public function __call($name, $arguments) {
        // Try to emit custom function within protocol
        if (method_exists($this->protocol, $name)) {
            try {
                return call_user_func(array($this->protocol, $name), $this, $arguments);
            } catch (\Exception $e) {
                Worker::log($e);
                exit(250);
            } catch (\Error $e) {
                Worker::log($e);
                exit(250);
            }
        }
    }

    /**
     * Construct.
     *
     * @param resource $socket
     * @param string   $remote_address
     */
    public function __construct($socket, $remote_address = '') {
        self::$statistics['connection_count'] ++;
        $this->id = $this->_id = self::$idRecorder++;
        if (self::$idRecorder === PHP_INT_MAX) {
            self::$idRecorder = 0;
        }
        $this->socket = $socket;
        stream_set_blocking($this->socket, 0);
        // Compatible with hhvm
        if (function_exists('stream_set_read_buffer')) {
            stream_set_read_buffer($this->socket, 0);
        }
        Worker::$globalEvent->add($this->socket, EventInterface::EV_READ, array($this, 'baseRead'));
        $this->maxSendBufferSize = self::$defaultMaxSendBufferSize;
        $this->maxPackageSize = self::$defaultMaxPackageSize;
        $this->remoteAddress = $remote_address;
        static::$connections[$this->id] = $this;
    }

    /**
     * Get status.
     *
     * @param bool $raw_output
     *
     * @return int
     */
    public function getStatus($raw_output = true) {
        if ($raw_output) {
            return $this->status;
        }
        return self::$statusToString[$this->status];
    }

    /**
     * Sends data on the connection.
     *
     * @param string $send_buffer
     * @param bool  $raw
     * @return bool|null
     */
    public function send($send_buffer, $raw = false) {
        if ($this->status === self::STATUS_CLOSING || $this->status === self::STATUS_CLOSED) {
            return false;
        }
        // Try to call protocol::encode($send_buffer) before sending.
        if (false === $raw && $this->protocol !== null) {
            $parser = $this->protocol;
            $send_buffer = $parser::encode($send_buffer, $this);
            if ($send_buffer === '') {
                return null;
            }
        }
        if ($this->status !== self::STATUS_ESTABLISHED ||
                ($this->transport === 'ssl' && $this->sslHandshakeCompleted !== true)
        ) {
            if ($this->sendBuffer) {
                if ($this->bufferIsFull()) {
                    self::$statistics['send_fail'] ++;
                    return false;
                }
            }
            $this->sendBuffer .= $send_buffer;
            $this->checkBufferWillFull();
            return null;
        }
        // Attempt to send data directly.
        if ($this->sendBuffer === '') {
            if ($this->transport === 'ssl') {
                Worker::$globalEvent->add($this->socket, EventInterface::EV_WRITE, array($this, 'baseWrite'));
                $this->sendBuffer = $send_buffer;
                $this->checkBufferWillFull();
                return null;
            }
            set_error_handler(function() {
                
            });
            $len = fwrite($this->socket, $send_buffer);
            restore_error_handler();
            // send successful.
            if ($len === strlen($send_buffer)) {
                $this->bytesWritten += $len;
                return true;
            }
            // Send only part of the data.
            if ($len > 0) {
                $this->sendBuffer = substr($send_buffer, $len);
                $this->bytesWritten += $len;
            } else {
                // Connection closed?
                if (!is_resource($this->socket) || feof($this->socket)) {
                    self::$statistics['send_fail'] ++;
                    if ($this->onError) {
                        try {
                            call_user_func($this->onError, $this, WORKERMAN_SEND_FAIL, 'client closed');
                        } catch (\Exception $e) {
                            Worker::log($e);
                            exit(250);
                        } catch (\Error $e) {
                            Worker::log($e);
                            exit(250);
                        }
                    }
                    $this->destroy();
                    return false;
                }
                $this->sendBuffer = $send_buffer;
            }
            Worker::$globalEvent->add($this->socket, EventInterface::EV_WRITE, array($this, 'baseWrite'));
            // Check if the send buffer will be full.
            $this->checkBufferWillFull();
            return null;
        } else {
            if ($this->bufferIsFull()) {
                self::$statistics['send_fail'] ++;
                return false;
            }
            $this->sendBuffer .= $send_buffer;
            // Check if the send buffer is full.
            $this->checkBufferWillFull();
        }
    }

    /**
     * Get remote IP.
     *
     * @return string
     */
    public function getRemoteIp() {
        $pos = strrpos($this->remoteAddress, ':');
        if ($pos) {
            return substr($this->remoteAddress, 0, $pos);
        }
        return '';
    }

    /**
     * Get remote port.
     *
     * @return int
     */
    public function getRemotePort() {
        if ($this->remoteAddress) {
            return (int) substr(strrchr($this->remoteAddress, ':'), 1);
        }
        return 0;
    }

    /**
     * Get remote address.
     *
     * @return string
     */
    public function getRemoteAddress() {
        return $this->remoteAddress;
    }

    /**
     * Get local IP.
     *
     * @return string
     */
    public function getLocalIp() {
        $address = $this->getLocalAddress();
        $pos = strrpos($address, ':');
        if (!$pos) {
            return '';
        }
        return substr($address, 0, $pos);
    }

    /**
     * Get local port.
     *
     * @return int
     */
    public function getLocalPort() {
        $address = $this->getLocalAddress();
        $pos = strrpos($address, ':');
        if (!$pos) {
            return 0;
        }
        return (int) substr(strrchr($address, ':'), 1);
    }

    /**
     * Get local address.
     *
     * @return string
     */
    public function getLocalAddress() {
        return (string) @streamsocket_get_name($this->socket, false);
    }

    /**
     * Get send buffer queue size.
     *
     * @return integer
     */
    public function getSendBufferQueueSize() {
        return strlen($this->sendBuffer);
    }

    /**
     * Get recv buffer queue size.
     *
     * @return integer
     */
    public function getRecvBufferQueueSize() {
        return strlen($this->recvBuffer);
    }

    /**
     * Is ipv4.
     *
     * return bool.
     */
    public function isIpV4() {
        if ($this->transport === 'unix') {
            return false;
        }
        return strpos($this->getRemoteIp(), ':') === false;
    }

    /**
     * Is ipv6.
     *
     * return bool.
     */
    public function isIpV6() {
        if ($this->transport === 'unix') {
            return false;
        }
        return strpos($this->getRemoteIp(), ':') !== false;
    }

    /**
     * Pauses the reading of data. That is onMessage will not be emitted. Useful to throttle back an upload.
     *
     * @return void
     */
    public function pauseRecv() {
        Worker::$globalEvent->del($this->socket, EventInterface::EV_READ);
        $this->isPaused = true;
    }

    /**
     * Resumes reading after a call to pauseRecv.
     *
     * @return void
     */
    public function resumeRecv() {
        if ($this->isPaused === true) {
            Worker::$globalEvent->add($this->socket, EventInterface::EV_READ, array($this, 'baseRead'));
            $this->isPaused = false;
            $this->baseRead($this->socket, false);
        }
    }

    /**
     * Base read handler.
     *
     * @param resource $socket
     * @param bool $check_eof
     * @return void
     */
    public function baseRead($socket, $check_eof = true) {
        // SSL handshake.
        if ($this->transport === 'ssl' && $this->sslHandshakeCompleted !== true) {
            if ($this->doSslHandshake($socket)) {
                $this->sslHandshakeCompleted = true;
                if ($this->sendBuffer) {
                    Worker::$globalEvent->add($socket, EventInterface::EV_WRITE, array($this, 'baseWrite'));
                }
            } else {
                return;
            }
        }
        set_error_handler(function() {
            
        });
        $buffer = fread($socket, self::READ_BUFFER_SIZE);
        restore_error_handler();
        // Check connection closed.
        if ($buffer === '' || $buffer === false) {
            if ($check_eof && (feof($socket) || !is_resource($socket) || $buffer === false)) {
                $this->destroy();
                return;
            }
        } else {
            $this->bytesRead += strlen($buffer);
            $this->recvBuffer .= $buffer;
        }
        // If the application layer protocol has been set up.
        if ($this->protocol !== null) {
            $parser = $this->protocol;
            while ($this->recvBuffer !== '' && !$this->isPaused) {
                // The current packet length is known.
                if ($this->_currentPackageLength) {
                    // Data is not enough for a package.
                    if ($this->_currentPackageLength > strlen($this->recvBuffer)) {
                        break;
                    }
                } else {
                    // Get current package length.
                    set_error_handler(function($code, $msg, $file, $line) {
                        Worker::safeEcho("$msg in file $file on line $line\n");
                    });
                    $this->_currentPackageLength = $parser::input($this->recvBuffer, $this);
                    restore_error_handler();
                    // The packet length is unknown.
                    if ($this->_currentPackageLength === 0) {
                        break;
                    } elseif ($this->_currentPackageLength > 0 && $this->_currentPackageLength <= $this->maxPackageSize) {
                        // Data is not enough for a package.
                        if ($this->_currentPackageLength > strlen($this->recvBuffer)) {
                            break;
                        }
                    } // Wrong package.
                    else {
                        Worker::safeEcho('error package. package_length=' . var_export($this->_currentPackageLength, true));
                        $this->destroy();
                        return;
                    }
                }
                // The data is enough for a packet.
                self::$statistics['total_request'] ++;
                // The current packet length is equal to the length of the buffer.
                if (strlen($this->recvBuffer) === $this->_currentPackageLength) {
                    $one_request_buffer = $this->recvBuffer;
                    $this->recvBuffer = '';
                } else {
                    // Get a full package from the buffer.
                    $one_request_buffer = substr($this->recvBuffer, 0, $this->_currentPackageLength);
                    // Remove the current package from the receive buffer.
                    $this->recvBuffer = substr($this->recvBuffer, $this->_currentPackageLength);
                }
                // Reset the current packet length to 0.
                $this->_currentPackageLength = 0;
                if (!$this->onMessage) {
                    continue;
                }
                try {
                    // Decode request buffer before Emitting onMessage callback.
                    call_user_func($this->onMessage, $this, $parser::decode($one_request_buffer, $this));
                } catch (\Exception $e) {
                    Worker::log($e);
                    exit(250);
                } catch (\Error $e) {
                    Worker::log($e);
                    exit(250);
                }
            }
            return;
        }
        if ($this->recvBuffer === '' || $this->isPaused) {
            return;
        }
        // Applications protocol is not set.
        self::$statistics['total_request'] ++;
        if (!$this->onMessage) {
            $this->recvBuffer = '';
            return;
        }
        try {
            call_user_func($this->onMessage, $this, $this->recvBuffer);
        } catch (\Exception $e) {
            Worker::log($e);
            exit(250);
        } catch (\Error $e) {
            Worker::log($e);
            exit(250);
        }
        // Clean receive buffer.
        $this->recvBuffer = '';
    }

    /**
     * Base write handler.
     *
     * @return void|bool
     */
    public function baseWrite() {
        set_error_handler(function() {
            
        });
        if ($this->transport === 'ssl') {
            $len = fwrite($this->socket, $this->sendBuffer, 8192);
        } else {
            $len = fwrite($this->socket, $this->sendBuffer);
        }
        restore_error_handler();
        if ($len === strlen($this->sendBuffer)) {
            $this->bytesWritten += $len;
            Worker::$globalEvent->del($this->socket, EventInterface::EV_WRITE);
            $this->sendBuffer = '';
            // Try to emit onBufferDrain callback when the send buffer becomes empty.
            if ($this->onBufferDrain) {
                try {
                    call_user_func($this->onBufferDrain, $this);
                } catch (\Exception $e) {
                    Worker::log($e);
                    exit(250);
                } catch (\Error $e) {
                    Worker::log($e);
                    exit(250);
                }
            }
            if ($this->status === self::STATUS_CLOSING) {
                $this->destroy();
            }
            return true;
        }
        if ($len > 0) {
            $this->bytesWritten += $len;
            $this->sendBuffer = substr($this->sendBuffer, $len);
        } else {
            self::$statistics['send_fail'] ++;
            $this->destroy();
        }
    }

    /**
     * SSL handshake.
     *
     * @param $socket
     * @return bool
     */
    public function doSslHandshake($socket) {
        if (feof($socket)) {
            $this->destroy();
            return false;
        }
        $async = $this instanceof CDaemon_Listener_Connection_AsyncTcpConnection;

        /**
         *  We disabled ssl3 because https://blog.qualys.com/ssllabs/2014/10/15/ssl-3-is-dead-killed-by-the-poodle-attack.
         *  You can enable ssl3 by the codes below.
         */
        /* if($async){
          $type = STREAM_CRYPTO_METHOD_SSLv2_CLIENT | STREAM_CRYPTO_METHOD_SSLv23_CLIENT | STREAM_CRYPTO_METHOD_SSLv3_CLIENT;
          }else{
          $type = STREAM_CRYPTO_METHOD_SSLv2_SERVER | STREAM_CRYPTO_METHOD_SSLv23_SERVER | STREAM_CRYPTO_METHOD_SSLv3_SERVER;
          } */

        if ($async) {
            $type = STREAM_CRYPTO_METHOD_SSLv2_CLIENT | STREAM_CRYPTO_METHOD_SSLv23_CLIENT;
        } else {
            $type = STREAM_CRYPTO_METHOD_SSLv2_SERVER | STREAM_CRYPTO_METHOD_SSLv23_SERVER;
        }

        // Hidden error.
        set_error_handler(function($errno, $errstr, $file) {
            
                Worker::safeEcho("SSL handshake error: $errstr \n");
            
        });
        $ret = streamsocket_enable_crypto($socket, true, $type);
        restore_error_handler();
        // Negotiation has failed.
        if (false === $ret) {
            $this->destroy();
            return false;
        } elseif (0 === $ret) {
            // There isn't enough data and should try again.
            return false;
        }
        if (isset($this->onSslHandshake)) {
            try {
                call_user_func($this->onSslHandshake, $this);
            } catch (\Exception $e) {
                Worker::log($e);
                exit(250);
            } catch (\Error $e) {
                Worker::log($e);
                exit(250);
            }
        }
        return true;
    }

    /**
     * This method pulls all the data out of a readable stream, and writes it to the supplied destination.
     *
     * @param CDaemon_Listener_Connection_TcpConnection $dest
     * @return void
     */
    public function pipe($dest) {
        $source = $this;
        $this->onMessage = function ($source, $data) use ($dest) {
            $dest->send($data);
        };
        $this->onClose = function ($source) use ($dest) {
            $dest->destroy();
        };
        $dest->onBufferFull = function ($dest) use ($source) {
            $source->pauseRecv();
        };
        $dest->onBufferDrain = function ($dest) use ($source) {
            $source->resumeRecv();
        };
    }

    /**
     * Remove $length of data from receive buffer.
     *
     * @param int $length
     * @return void
     */
    public function consumeRecvBuffer($length) {
        $this->recvBuffer = substr($this->recvBuffer, $length);
    }

    /**
     * Close connection.
     *
     * @param mixed $data
     * @param bool $raw
     * @return void
     */
    public function close($data = null, $raw = false) {
        if ($this->status === self::STATUS_CLOSING || $this->status === self::STATUS_CLOSED) {
            return;
        } else {
            if ($data !== null) {
                $this->send($data, $raw);
            }
            $this->status = self::STATUS_CLOSING;
        }
        if ($this->sendBuffer === '') {
            $this->destroy();
        } else {
            $this->pauseRecv();
        }
    }

    /**
     * Get the real socket.
     *
     * @return resource
     */
    public function getSocket() {
        return $this->socket;
    }

    /**
     * Check whether the send buffer will be full.
     *
     * @return void
     */
    protected function checkBufferWillFull() {
        if ($this->maxSendBufferSize <= strlen($this->sendBuffer)) {
            if ($this->onBufferFull) {
                try {
                    call_user_func($this->onBufferFull, $this);
                } catch (\Exception $e) {
                    Worker::log($e);
                    exit(250);
                } catch (\Error $e) {
                    Worker::log($e);
                    exit(250);
                }
            }
        }
    }

    /**
     * Whether send buffer is full.
     *
     * @return bool
     */
    protected function bufferIsFull() {
        // Buffer has been marked as full but still has data to send then the packet is discarded.
        if ($this->maxSendBufferSize <= strlen($this->sendBuffer)) {
            if ($this->onError) {
                try {
                    call_user_func($this->onError, $this, WORKERMAN_SEND_FAIL, 'send buffer full and drop package');
                } catch (\Exception $e) {
                    Worker::log($e);
                    exit(250);
                } catch (\Error $e) {
                    Worker::log($e);
                    exit(250);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Whether send buffer is Empty.
     *
     * @return bool
     */
    public function bufferIsEmpty() {
        return empty($this->sendBuffer);
    }

    /**
     * Destroy connection.
     *
     * @return void
     */
    public function destroy() {
        // Avoid repeated calls.
        if ($this->status === self::STATUS_CLOSED) {
            return;
        }
        // Remove event listener.
        Worker::$globalEvent->del($this->socket, EventInterface::EV_READ);
        Worker::$globalEvent->del($this->socket, EventInterface::EV_WRITE);
        // Close socket.
        set_error_handler(function() {
            
        });
        fclose($this->socket);
        restore_error_handler();
        $this->status = self::STATUS_CLOSED;
        // Try to emit onClose callback.
        if ($this->onClose) {
            try {
                call_user_func($this->onClose, $this);
            } catch (\Exception $e) {
                Worker::log($e);
                exit(250);
            } catch (\Error $e) {
                Worker::log($e);
                exit(250);
            }
        }
        // Try to emit protocol::onClose
        if ($this->protocol && method_exists($this->protocol, 'onClose')) {
            try {
                call_user_func(array($this->protocol, 'onClose'), $this);
            } catch (\Exception $e) {
                Worker::log($e);
                exit(250);
            } catch (\Error $e) {
                Worker::log($e);
                exit(250);
            }
        }
        $this->sendBuffer = $this->recvBuffer = '';
        if ($this->status === self::STATUS_CLOSED) {
            // Cleaning up the callback to avoid memory leaks.
            $this->onMessage = $this->onClose = $this->onError = $this->onBufferFull = $this->onBufferDrain = null;
            // Remove from worker->connections.
            if ($this->worker) {
                unset($this->worker->connections[$this->_id]);
            }
            unset(static::$connections[$this->_id]);
        }
    }

    /**
     * Destruct.
     *
     * @return void
     */
    public function __destruct() {
        static $mod;
        self::$statistics['connection_count'] --;
        if (Worker::getGracefulStop()) {
            if (!isset($mod)) {
                $mod = ceil((self::$statistics['connection_count'] + 1) / 3);
            }
            if (0 === self::$statistics['connection_count'] % $mod) {
                Worker::log('worker[' . posix_getpid() . '] remains ' . self::$statistics['connection_count'] . ' connection(s)');
            }
            if (0 === self::$statistics['connection_count']) {
                Worker::stopAll();
            }
        }
    }

}
