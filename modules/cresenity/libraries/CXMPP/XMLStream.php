<?php

/**
 * XMPPHP XML Stream
 *
 * @category    Xmpphp
 * @package     XMPPHP
 *
 * @author Nathanael C. Fritz <JID: fritzy@netflint.net>
 * @author Stephan Wentz <JID: stephan@jabber.wentz.it>
 * @author Michael Garvin <JID: gar@netflint.net>
 * @copyright  2008 Nathanael C. Fritz
 *
 * @version Release:1.0
 */
class CXMPP_XMLStream {
    /**
     * @var resource
     */
    protected $socket;

    /**
     * @var XMLParser
     */
    protected $parser;

    /**
     * @var string
     */
    protected $buffer;

    /**
     * @var int
     */
    protected $xml_depth = 0;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $stream_start = '<stream>';

    /**
     * @var string
     */
    protected $stream_end = '</stream>';

    /**
     * @var bool
     */
    protected $disconnected = false;

    /**
     * @var bool
     */
    protected $sent_disconnect = false;

    /**
     * @var array
     */
    protected $ns_map = [];

    /**
     * @var array
     */
    protected $current_ns = [];

    /**
     * @var array
     */
    protected $xmlobj = null;

    /**
     * @var array
     */
    protected $nshandlers = [];

    /**
     * @var array
     */
    protected $xpathhandlers = [];

    /**
     * @var array
     */
    protected $idhandlers = [];

    /**
     * @var array
     */
    protected $eventhandlers = [];

    /**
     * @var int
     */
    protected $lastid = 0;

    /**
     * @var string
     */
    protected $default_ns;

    /**
     * @var string
     */
    protected $until = '';

    /**
     * @var string
     */
    protected $until_count = '';

    /**
     * @var array
     */
    protected $until_happened = false;

    /**
     * @var array
     */
    protected $until_payload = [];

    /**
     * @var XMPPHP_Log
     */
    protected $log;

    /**
     * @var bool
     */
    protected $reconnect = true;

    /**
     * @var bool
     */
    protected $been_reset = false;

    /**
     * @var bool
     */
    protected $is_server;

    /**
     * @var float
     */
    protected $last_send = 0;

    /**
     * @var bool
     */
    protected $use_ssl = false;

    /**
     * @var int
     */
    protected $reconnectTimeout = 30;

    /**
     * Constructor
     *
     * @param string $host
     * @param string $port
     * @param bool   $printlog
     * @param string $loglevel
     * @param bool   $is_server
     */
    public function __construct($host = null, $port = null, $printlog = false, $loglevel = null, $is_server = false) {
        $this->reconnect = !$is_server;
        $this->is_server = $is_server;
        $this->host = $host;
        $this->port = $port;
        $this->setupParser();
        $this->log = new CXMPP_Log($printlog, $loglevel);
    }

    /**
     * Destructor
     * Cleanup connection
     */
    public function __destruct() {
        if (!$this->disconnected && $this->socket) {
            $this->disconnect();
        }
    }

    /**
     * Return the log instance
     *
     * @return XMPPHP_Log
     */
    public function getLog() {
        return $this->log;
    }

    /**
     * Get next ID
     *
     * @return int
     */
    public function getId() {
        $this->lastid++;
        return $this->lastid;
    }

    /**
     * Set SSL
     *
     * @param mixed $use
     *
     * @return int
     */
    public function useSSL($use = true) {
        $this->use_ssl = $use;
    }

    /**
     * Add ID Handler
     *
     * @param int    $id
     * @param string $pointer
     * @param string $obj
     */
    public function addIdHandler($id, $pointer, $obj = null) {
        $this->idhandlers[$id] = [$pointer, $obj];
    }

    /**
     * Add Handler
     *
     * @param string $name
     * @param string $ns
     * @param string $pointer
     * @param string $obj
     * @param int    $depth
     */
    public function addHandler($name, $ns, $pointer, $obj = null, $depth = 1) {
        //TODO deprication warning
        $this->nshandlers[] = [$name, $ns, $pointer, $obj, $depth];
    }

    /**
     * Add XPath Handler
     *
     * @param string     $xpath
     * @param string     $pointer
     * @param null|mixed $obj
     */
    public function addXPathHandler($xpath, $pointer, $obj = null) {
        if (preg_match_all("/\(?{[^\}]+}\)?(\/?)[^\/]+/", $xpath, $regs)) {
            $ns_tags = $regs[0];
        } else {
            $ns_tags = [$xpath];
        }
        foreach ($ns_tags as $ns_tag) {
            list($l, $r) = explode('}', $ns_tag);
            if ($r != null) {
                $xpart = [substr($l, 1), $r];
            } else {
                $xpart = [null, $l];
            }
            $xpath_array[] = $xpart;
        }
        $this->xpathhandlers[] = [$xpath_array, $pointer, $obj];
    }

    /**
     * Add Event Handler
     *
     * @param int    $name
     * @param string $pointer
     * @param string $obj
     */
    public function addEventHandler($name, $pointer, $obj) {
        $this->eventhandlers[] = [$name, $pointer, $obj];
    }

    /**
     * Connect to XMPP Host
     *
     * @param int  $timeout
     * @param bool $persistent
     * @param bool $sendinit
     */
    public function connect($timeout = 30, $persistent = false, $sendinit = true) {
        $this->sent_disconnect = false;
        $starttime = time();

        do {
            $this->disconnected = false;
            $this->sent_disconnect = false;
            if ($persistent) {
                $conflag = STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT;
            } else {
                $conflag = STREAM_CLIENT_CONNECT;
            }
            $conntype = 'tcp';
            if ($this->use_ssl) {
                $conntype = 'ssl';
            }
            $this->log->log("Connecting to $conntype://{$this->host}:{$this->port}");
            try {
                $this->socket = @stream_socket_client("$conntype://{$this->host}:{$this->port}", $errno, $errstr, $timeout, $conflag);
            } catch (Exception $e) {
                throw new CXMPP_Exception($e->getMessage());
            }
            if (!$this->socket) {
                $this->log->log('Could not connect.', CXMPP_Log::LEVEL_ERROR);
                $this->disconnected = true;
                // Take it easy for a few seconds
                sleep(min($timeout, 5));
            }
        } while (!$this->socket && (time() - $starttime) < $timeout);

        if ($this->socket) {
            stream_set_blocking($this->socket, 1);
            if ($sendinit) {
                $this->send($this->stream_start);
            }
        } else {
            throw new CXMPP_Exception('Could not connect before timeout.');
        }
    }

    /**
     * Reconnect XMPP Host
     */
    public function doReconnect() {
        if (!$this->is_server) {
            $this->log->log("Reconnecting ($this->reconnectTimeout)...", CXMPP_Log::LEVEL_WARNING);
            $this->connect($this->reconnectTimeout, false, false);
            $this->reset();
            $this->event('reconnect');
        }
    }

    public function setReconnectTimeout($timeout) {
        $this->reconnectTimeout = $timeout;
    }

    /**
     * Disconnect from XMPP Host
     */
    public function disconnect() {
        $this->log->log('Disconnecting...', CXMPP_Log::LEVEL_VERBOSE);
        if (false == (bool) $this->socket) {
            return;
        }
        $this->reconnect = false;
        $this->send($this->stream_end);
        $this->sent_disconnect = true;
        $this->processUntil('end_stream', 5);
        $this->disconnected = true;
    }

    /**
     * Are we are disconnected?
     *
     * @return bool
     */
    public function isDisconnected() {
        return $this->disconnected;
    }

    /**
     * Core reading tool
     * 0 -> only read if data is immediately ready
     * NULL -> wait forever and ever
     * integer -> process for this amount of time
     *
     * @param mixed $maximum
     */
    private function __process($maximum = 5) {
        $remaining = $maximum;

        do {
            $starttime = (microtime(true) * 1000000);
            $read = [$this->socket];
            $write = [];
            $except = [];
            if (is_null($maximum)) {
                $secs = null;
                $usecs = null;
            } elseif ($maximum == 0) {
                $secs = 0;
                $usecs = 0;
            } else {
                $usecs = $remaining % 1000000;
                $secs = floor(($remaining - $usecs) / 1000000);
            }
            $updated = @stream_select($read, $write, $except, $secs, $usecs);
            if ($updated === false) {
                $this->log->log('Error on stream_select()', CXMPP_Log::LEVEL_VERBOSE);
                if ($this->reconnect) {
                    $this->doReconnect();
                } else {
                    fclose($this->socket);
                    $this->socket = null;
                    return false;
                }
            } elseif ($updated > 0) {
                // XXX: Is this big enough?
                $buff = @fread($this->socket, 4096);
                if (!$buff) {
                    if ($this->reconnect) {
                        $this->doReconnect();
                    } else {
                        fclose($this->socket);
                        $this->socket = null;
                        return false;
                    }
                }
                $this->log->log("RECV: $buff", CXMPP_Log::LEVEL_VERBOSE);
                xml_parse($this->parser, $buff, false);
            } else {
                // $updated == 0 means no changes during timeout.
            }
            $endtime = (microtime(true) * 1000000);
            $time_past = $endtime - $starttime;
            $remaining = $remaining - $time_past;
        } while (is_null($maximum) || $remaining > 0);
        return true;
    }

    /**
     * Process
     *
     * @return string
     */
    public function process() {
        $this->__process(null);
    }

    /**
     * Process until a timeout occurs
     *
     * @param int $timeout
     *
     * @return string
     */
    public function processTime($timeout = null) {
        if (is_null($timeout)) {
            return $this->__process(null);
        } else {
            return $this->__process($timeout * 1000000);
        }
    }

    /**
     * Process until a specified event or a timeout occurs
     *
     * @param string|array $event
     * @param int          $timeout
     *
     * @return string
     */
    public function processUntil($event, $timeout = -1) {
        $start = time();
        if (!is_array($event)) {
            $event = [$event];
        }
        $this->until[] = $event;
        end($this->until);
        $event_key = key($this->until);
        reset($this->until);
        $this->until_count[$event_key] = 0;
        $updated = '';
        while (!$this->disconnected and $this->until_count[$event_key] < 1 and (time() - $start < $timeout or $timeout == -1)) {
            $this->__process();
        }
        if (array_key_exists($event_key, $this->until_payload)) {
            $payload = $this->until_payload[$event_key];
            unset($this->until_payload[$event_key], $this->until_count[$event_key], $this->until[$event_key]);
        } else {
            $payload = [];
        }
        return $payload;
    }

    /**
     * Obsolete?
     *
     * @param mixed $socket
     */
    public function Xapply_socket($socket) {
        $this->socket = $socket;
    }

    /**
     * XML start callback
     *
     * @param resource $parser
     * @param string   $name
     * @param mixed    $attr
     *
     * @see xml_set_element_handler
     */
    public function startXML($parser, $name, $attr) {
        if ($this->been_reset) {
            $this->been_reset = false;
            $this->xml_depth = 0;
        }
        $this->xml_depth++;
        if (array_key_exists('XMLNS', $attr)) {
            $this->current_ns[$this->xml_depth] = $attr['XMLNS'];
        } else {
            $this->current_ns[$this->xml_depth] = $this->current_ns[$this->xml_depth - 1];
            if (!$this->current_ns[$this->xml_depth]) {
                $this->current_ns[$this->xml_depth] = $this->default_ns;
            }
        }
        $ns = $this->current_ns[$this->xml_depth];
        foreach ($attr as $key => $value) {
            if (strstr($key, ':')) {
                $key = explode(':', $key);
                $key = $key[1];
                $this->ns_map[$key] = $value;
            }
        }
        if (!strstr($name, ':') === false) {
            $name = explode(':', $name);
            $ns = $this->ns_map[$name[0]];
            $name = $name[1];
        }
        $obj = new CXMPP_XMLObj($name, $ns, $attr);
        if ($this->xml_depth > 1) {
            $this->xmlobj[$this->xml_depth - 1]->subs[] = $obj;
        }
        $this->xmlobj[$this->xml_depth] = $obj;
    }

    /**
     * XML end callback
     *
     * @param resource $parser
     * @param string   $name
     *
     * @see xml_set_element_handler
     */
    public function endXML($parser, $name) {
        //$this->log->log("Ending $name",  XMPPHP_Log::LEVEL_DEBUG);
        //print "$name\n";
        if ($this->been_reset) {
            $this->been_reset = false;
            $this->xml_depth = 0;
        }
        $this->xml_depth--;
        if ($this->xml_depth == 1) {
            //clean-up old objects
            //$found = false; #FIXME This didn't appear to be in use --Gar
            foreach ($this->xpathhandlers as $handler) {
                if (is_array($this->xmlobj) && array_key_exists(2, $this->xmlobj)) {
                    $searchxml = $this->xmlobj[2];
                    $nstag = array_shift($handler[0]);
                    if (($nstag[0] == null or $searchxml->ns == $nstag[0]) and ($nstag[1] == '*' or $nstag[1] == $searchxml->name)) {
                        foreach ($handler[0] as $nstag) {
                            if ($searchxml !== null and $searchxml->hasSub($nstag[1], $ns = $nstag[0])) {
                                $searchxml = $searchxml->sub($nstag[1], $ns = $nstag[0]);
                            } else {
                                $searchxml = null;
                                break;
                            }
                        }
                        if ($searchxml !== null) {
                            if ($handler[2] === null) {
                                $handler[2] = $this;
                            }
                            $this->log->log("Calling {$handler[1]}", CXMPP_Log::LEVEL_DEBUG);
                            $handler[2]->$handler[1]($this->xmlobj[2]);
                        }
                    }
                }
            }
            foreach ($this->nshandlers as $handler) {
                if ($handler[4] != 1 and array_key_exists(2, $this->xmlobj) and $this->xmlobj[2]->hasSub($handler[0])) {
                    $searchxml = $this->xmlobj[2]->sub($handler[0]);
                } elseif (is_array($this->xmlobj) and array_key_exists(2, $this->xmlobj)) {
                    $searchxml = $this->xmlobj[2];
                }
                if ($searchxml !== null and $searchxml->name == $handler[0] and ($searchxml->ns == $handler[1] or (!$handler[1] and $searchxml->ns == $this->default_ns))) {
                    if ($handler[3] === null) {
                        $handler[3] = $this;
                    }
                    $this->log->log("Calling {$handler[2]}", CXMPP_Log::LEVEL_DEBUG);
                    $handler[3]->$handler[2]($this->xmlobj[2]);
                }
            }
            foreach ($this->idhandlers as $id => $handler) {
                if (array_key_exists('id', $this->xmlobj[2]->attrs) and $this->xmlobj[2]->attrs['id'] == $id) {
                    if ($handler[1] === null) {
                        $handler[1] = $this;
                    }
                    $handler[1]->$handler[0]($this->xmlobj[2]);
                    //id handlers are only used once
                    unset($this->idhandlers[$id]);
                    break;
                }
            }
            if (is_array($this->xmlobj)) {
                $this->xmlobj = array_slice($this->xmlobj, 0, 1);
                if (isset($this->xmlobj[0]) && $this->xmlobj[0] instanceof CXMPP_XMLObj) {
                    $this->xmlobj[0]->subs = null;
                }
            }
            unset($this->xmlobj[2]);
        }
        if ($this->xml_depth == 0 and !$this->been_reset) {
            if (!$this->disconnected) {
                if (!$this->sent_disconnect) {
                    $this->send($this->stream_end);
                }
                $this->disconnected = true;
                $this->sent_disconnect = true;
                fclose($this->socket);
                if ($this->reconnect) {
                    $this->doReconnect();
                }
            }
            $this->event('end_stream');
        }
    }

    /**
     * XML character callback
     *
     * @param resource $parser
     * @param string   $data
     *
     * @see xml_set_character_data_handler
     */
    public function charXML($parser, $data) {
        if (array_key_exists($this->xml_depth, $this->xmlobj)) {
            $this->xmlobj[$this->xml_depth]->data .= $data;
        }
    }

    /**
     * Event?
     *
     * @param string $name
     * @param string $payload
     */
    public function event($name, $payload = null) {
        $this->log->log("EVENT: $name", CXMPP_Log::LEVEL_DEBUG);
        foreach ($this->eventhandlers as $handler) {
            if ($name == $handler[0]) {
                if ($handler[2] === null) {
                    $handler[2] = $this;
                }
                $handler[2]->$handler[1]($payload);
            }
        }
        foreach ($this->until as $key => $until) {
            if (is_array($until)) {
                if (in_array($name, $until)) {
                    $this->until_payload[$key][] = [$name, $payload];
                    if (!isset($this->until_count[$key])) {
                        $this->until_count[$key] = 0;
                    }
                    $this->until_count[$key] += 1;
                    //$this->until[$key] = false;
                }
            }
        }
    }

    /**
     * Read from socket
     */
    public function read() {
        $buff = @fread($this->socket, 1024);
        if (!$buff) {
            if ($this->reconnect) {
                $this->doReconnect();
            } else {
                fclose($this->socket);
                return false;
            }
        }
        $this->log->log("RECV: $buff", CXMPP_Log::LEVEL_VERBOSE);
        xml_parse($this->parser, $buff, false);
    }

    /**
     * Send to socket
     *
     * @param string     $msg
     * @param null|mixed $timeout
     */
    public function send($msg, $timeout = null) {
        if (is_null($timeout)) {
            $secs = null;
            $usecs = null;
        } elseif ($timeout == 0) {
            $secs = 0;
            $usecs = 0;
        } else {
            $maximum = $timeout * 1000000;
            $usecs = $maximum % 1000000;
            $secs = floor(($maximum - $usecs) / 1000000);
        }

        $read = [];
        $write = [$this->socket];
        $except = [];

        $select = @stream_select($read, $write, $except, $secs, $usecs);

        if ($select === false) {
            $this->log->log('ERROR sending message; reconnecting.');
            $this->doReconnect();
            // TODO: retry send here
            return false;
        } elseif ($select > 0) {
            $this->log->log('Socket is ready; send it.', CXMPP_Log::LEVEL_VERBOSE);
        } else {
            $this->log->log('Socket is not ready; break.', CXMPP_Log::LEVEL_ERROR);
            return false;
        }

        $sentbytes = @fwrite($this->socket, $msg);
        $this->log->log('SENT: ' . mb_substr($msg, 0, $sentbytes, '8bit'), CXMPP_Log::LEVEL_VERBOSE);
        if ($sentbytes === false) {
            $this->log->log('ERROR sending message; reconnecting.', CXMPP_Log::LEVEL_ERROR);
            $this->doReconnect();
            return false;
        }
        $this->log->log("Successfully sent $sentbytes bytes.", CXMPP_Log::LEVEL_VERBOSE);
        return $sentbytes;
    }

    public function time() {
        list($usec, $sec) = explode(' ', microtime());
        return (float) $sec + (float) $usec;
    }

    /**
     * Reset connection
     */
    public function reset() {
        $this->xml_depth = 0;
        unset($this->xmlobj);
        $this->xmlobj = [];
        $this->setupParser();
        if (!$this->is_server) {
            $this->send($this->stream_start);
        }
        $this->been_reset = true;
    }

    /**
     * Setup the XML parser
     */
    public function setupParser() {
        $this->parser = xml_parser_create('UTF-8');
        xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, 'startXML', 'endXML');
        xml_set_character_data_handler($this->parser, 'charXML');
    }

    public function readyToProcess() {
        $read = [$this->socket];
        $write = [];
        $except = [];
        $updated = @stream_select($read, $write, $except, 0);
        return (($updated !== false) && ($updated > 0));
    }
}
