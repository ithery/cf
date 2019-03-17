<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 17, 2019, 8:53:57 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CDaemon_ListenerAbstract extends CDaemon_ServiceAbstract {

     /**
     * Default backlog. Backlog is the maximum length of the queue of pending connections.
     *
     * @var int
     */
    const DEFAULT_BACKLOG = 102400;

    /**
     * PHP built-in protocols.
     *
     * @var array
     */
    protected static $builtinTransports = array(
        'tcp' => 'tcp',
        'udp' => 'udp',
        'unix' => 'unix',
        'ssl' => 'tcp'
    );

    /**
     * Unix group of processes, needs appropriate privileges (usually root).
     *
     * @var string
     */
    public $group = '';

    /**
     * Unix user of processes, needs appropriate privileges (usually root).
     *
     * @var string
     */
    public $user = '';

    /**
     * Socket name. The format is like this http://0.0.0.0:80 .
     *
     * @var string
     */
    protected $socketName = '';

    /**
     * reuse port.
     *
     * @var bool
     */
    public $reusePort = false;

    /**
     * reloadable.
     *
     * @var bool
     */
    public $reloadable = true;

    /**
     * Context of socket.
     *
     * @var resource
     */
    protected $_context = null;

    /**
     * Construct.
     *
     * @param string $socketName
     * @param array  $contextOption
     */

    /**
     * Emitted when a socket connection is successfully established.
     *
     * @var callback
     */
    public $onConnect = null;

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
     * Transport layer protocol.
     *
     * @var string
     */
    public $transport = 'tcp';

    /**
     * Store all connections of clients.
     *
     * @var array
     */
    public $connections = array();

    /**
     * Application layer protocol.
     *
     * @var string
     */
    public $protocol = null;

    /**
     * Pause accept new connections or not.
     *
     * @var bool
     */
    protected $pauseAccept = true;

    /**
     * Listening socket.
     *
     * @var resource
     */
    protected $mainSocket = null;

    public function setSocket($socketName = '', $contextOption = array()) {

        // Context for socket.
        if (strlen($socketName) > 0) {
            $this->socketName = $socketName;
            if (!isset($contextOption['socket']['backlog'])) {
                $contextOption['socket']['backlog'] = static::DEFAULT_BACKLOG;
            }
            $this->context = stream_context_create($contextOption);
         
        }
    }

    /**
     * Accept a connection.
     *
     * @param resource $socket
     * @return void
     */
    public function acceptConnection() {
        // Accept a connection on server socket.
        set_error_handler(function() {
            
        });
        $newSocket = stream_socket_accept($this->mainSocket, 0, $remoteAddress);
        restore_error_handler();
        // Thundering herd.
        if (!$newSocket) {
            return;
        }
        
        $this->log('Accepted new Connection');
        // TcpConnection.
        $connection = new CDaemon_Worker_Connection_TcpConnection($newSocket, $remoteAddress);
        $this->connections[$connection->id] = $connection;
        $connection->worker = $this;
        $connection->protocol = $this->protocol;
        $connection->transport = $this->transport;
        $connection->onMessage = $this->onMessage;
        $connection->onClose = $this->onClose;
        $connection->onError = $this->onError;
        $connection->onBufferDrain = $this->onBufferDrain;
        $connection->onBufferFull = $this->onBufferFull;
        // Try to emit onConnect callback.
        if ($this->onConnect) {
            try {
                call_user_func($this->onConnect, $connection);
            } catch (\Exception $e) {
                $this->log($e);
                exit(250);
            } catch (\Error $e) {
                $this->log($e);
                exit(250);
            }
        }
    }

    /**
     * Resume accept new connections.
     *
     * @return void
     */
    public function resumeAccept() {
        // Register a listener to be notified when server socket is ready to read.
        if ($this->event && true === $this->pauseAccept && $this->mainSocket) {
            $this->log('Listen acceptConnection');
            if ($this->transport !== 'udp') {
                $this->event->listen(CDaemon_Worker_Constant::EV_LISTENER_READ, array($this, 'acceptConnection'));
            } else {
                $this->event->listen(CDaemon_Worker_Constant::EV_LISTENER_READ, array($this, 'acceptUdpConnection'));
            }
            $this->_pauseAccept = false;
        }
    }

    /**
     * Pause accept new connections.
     *
     * @return void
     */
    public function pauseAccept() {
        if (static::$globalEvent && false === $this->_pauseAccept && $this->_mainSocket) {
            static::$globalEvent->del($this->_mainSocket, CDaemon_Worker_Constant::EV_LISTENER_READ);
            $this->pauseAccept = true;
        }
    }

    /**
     * Get socket name.
     *
     * @return string
     */
    public function getSocketName() {
        return $this - socketName ? lcfirst($this->socketName) : 'none';
    }

    /**
     * Listen.
     *
     * @throws Exception
     */
    public function listen() {
        if (!$this->socketName) {
            return;
        }

        if (!$this->mainSocket) {
            // Get the application layer communication protocol and listening address.
            list($scheme, $address) = explode(':', $this->socketName, 2);
            
            // Check application layer protocol class.
            if (!isset(static::$builtinTransports[$scheme])) {
                $scheme = ucfirst($scheme);
                $this->protocol = substr($scheme, 0, 1) === '\\' ? $scheme : '\\Protocols\\' . $scheme;
                if (!class_exists($this->protocol)) {
                    $this->protocol = "CDaemon_Worker_Protocol_" . $scheme;
                    if (!class_exists($this->protocol)) {
                        throw new Exception("class " . $this->protocol . " not exist");
                    }
                }
                if (!isset(static::$builtinTransports[$this->transport])) {
                    throw new \Exception('Bad worker->transport ' . var_export($this->transport, true));
                }
            } else {
                $this->transport = $scheme;
            }
            $localSocket = static::$builtinTransports[$this->transport] . ":" . $address;
            // Flag.
            $flags = $this->transport === 'udp' ? STREAM_SERVER_BIND : STREAM_SERVER_BIND | STREAM_SERVER_LISTEN;
            $errno = 0;
            $errmsg = '';
            // SO_REUSEPORT.
            if ($this->reusePort) {
                stream_context_set_option($this->context, 'socket', 'so_reuseport', 1);
            }
            // Create an Internet or Unix domain server socket.
            
            $this->mainSocket = stream_socket_server($localSocket, $errno, $errmsg, $flags, $this->context);
            if (!$this->mainSocket) {
                
                throw new Exception($errmsg);
               
            }
            $this->log('Start listening '.$localSocket);
            
            if ($this->transport === 'ssl') {
                stream_socket_enable_crypto($this->mainSocket, false);
            } elseif ($this->transport === 'unix') {
                $socketFile = substr($address, 2);
                if ($this->user) {
                    chown($socketFile, $this->user);
                }
                if ($this->group) {
                    chgrp($socketFile, $this->group);
                }
            }
            // Try to open keepalive for tcp and disable Nagle algorithm.
            if (function_exists('socket_import_stream') && static::$builtinTransports[$this->transport] === 'tcp') {
                set_error_handler(function() {
                    
                });
                $socket = socket_import_stream($this->mainSocket);
                socket_set_option($socket, SOL_SOCKET, SO_KEEPALIVE, 1);
                socket_set_option($socket, SOL_TCP, TCP_NODELAY, 1);
                restore_error_handler();
            }
            // Non blocking.
            stream_set_blocking($this->mainSocket, 0);
        }
        $this->resumeAccept();
    }

    

    /**
     * Get unix user of current porcess.
     *
     * @return string
     */
    protected static function getCurrentUser() {
        $user_info = posix_getpwuid(posix_getuid());
        return $user_info['name'];
    }

    /**
     * Set unix user and group for current process.
     *
     * @return void
     */
    public function setUserAndGroup() {
        // Get uid.
        $user_info = posix_getpwnam($this->user);
        if (!$user_info) {
            static::log("Warning: User {$this->user} not exsits");
            return;
        }
        $uid = $user_info['uid'];
        // Get gid.
        if ($this->group) {
            $group_info = posix_getgrnam($this->group);
            if (!$group_info) {
                $this->log("Warning: Group {$this->group} not exsits");
                return;
            }
            $gid = $group_info['gid'];
        } else {
            $gid = $user_info['gid'];
        }
        // Set uid and gid.
        if ($uid != posix_getuid() || $gid != posix_getgid()) {
            if (!posix_setgid($gid) || !posix_initgroups($user_info['name'], $gid) || !posix_setuid($uid)) {
                $this->log("Warning: change gid or uid fail.");
            }
        }
    }

     /**
     * Unlisten.
     *
     * @return void
     */
    public function unlisten() {
        $this->pauseAccept();
        if ($this->mainSocket) {
            set_error_handler(function(){});
            fclose($this->mainSocket);
            restore_error_handler();
            $this->mainSocket = null;
        }
    }
    
   

}
