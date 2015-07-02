<?php

if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);


require_once dirname(__FILE__).DS.'CWebsocketClient'.DS.'CWebsocketClientSocketIOEngine.php';



class CWebsocketClient
{
    /** @var EngineInterface */
    private $engine;
    /** @var LoggerInterface */
    private $logger;
    private $isConnected = false;
    
	public static function factory($url, array $options = array(),$engine_name='socket.io',  $logger = null) {
		return new CWebsocketClient($url,$options,$engine_name,  $logger);
	}
	public function __construct($url,$options=array(),$engine_name='socket.io',  $logger = null) {
        switch($engine_name) {
			case 'socket.io':
				$this->engine = new CWebsocketClientSocketIOEngine($url,$options);
			break;
			default:
				throw new Exception('Engine '.$name.' not found');
			break;
		}
		
        $this->logger = $logger;
    }
    public function __destruct()
    {
        if (!$this->isConnected) {
            return;
        }
        $this->close();
    }
    /**
     * Connects to the websocket
     *
     * @param boolean $keep_alive keep alive the connection (not supported yet) ?
     * @return $this
     */
    public function initialize($keep_alive = false) {
        try {
            null !== $this->logger && $this->logger->debug('Connecting to the websocket');
            $this->engine->connect();
            null !== $this->logger && $this->logger->debug('Connected to the server');
            $this->isConnected = true;
            if (true === $keep_alive) {
                null !== $this->logger && $this->logger->debug('Keeping alive the connection to the websocket');
                $this->engine->keep_alive();
            }
        } catch (Exception $e) {
            null !== $this->logger && $this->logger->error('Could not connect to the server', array('exception' => $e));
            throw $e;
        }
        return $this;
    }
    /**
     * Reads a message from the socket
     *
     * @return MessageInterface Message read from the socket
     */
    public function read()
    {
        null !== $this->logger && $this->logger->debug('Reading a new message from the socket');
        return $this->engine->read();
    }
    /**
     * Emits a message through the engine
     *
     * @return $this
     */
    public function emit($event, array $args)
    {
        null !== $this->logger && $this->logger->debug('Sending a new message', array('event' => $event, 'args' => $args));
        $this->engine->emit($event, $args);
        return $this;
    }
    /**
     * Closes the connection
     *
     * @return $this
     */
    public function close()
    {
        null !== $this->logger && $this->logger->debug('Closing the connection to the websocket');
        $this->engine->close();
        $this->isConnected = false;
        return $this;
    }
    /**
     * Gets the engine used, for more advanced functions
     *
     * @return EngineInterface
     */
    public function getEngine()
    {
        return $this->engine;
    }
}

