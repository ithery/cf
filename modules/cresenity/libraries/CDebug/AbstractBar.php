<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 3:13:50 PM
 */
class CDebug_AbstractBar implements ArrayAccess {
    public static $useOpenHandlerWhenSendingDataHeaders = false;
    protected $data;

    /**
     * @var CDebug_Interface_DataCollectorInterface[]
     */
    protected $collectors = [];

    /**
     * Config of this bar
     *
     * @var CDebug_Bar_Config
     */
    protected $config;

    /**
     * @var CDebug_Bar_Interface_RequestIdGeneratorInterface
     */
    protected $requestIdGenerator;
    protected $requestId;
    protected $storage;

    /**
     * @var CDebug_Bar_Interface_HttpDriverInterface
     */
    protected $httpDriver;
    protected $stackSessionNamespace = 'PHPDEBUGBAR_STACK_DATA';
    protected $stackAlwaysUseSessionStorage = false;

    /**
     * @var CDebug_Bar_Renderer
     */
    protected $renderer;

    public function __construct(array $options = []) {
        $this->config = new CDebug_Bar_Config($options);
        $this->renderer = new CDebug_Bar_Renderer($this);
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options) {
        $this->config->setOptions($options);
        return $this;
    }

    /**
     * Adds a data collector
     *
     * @param DataCollectorInterface $collector
     *
     * @throws DebugBarException
     *
     * @return $this
     */
    public function addCollector(CDebug_Interface_DataCollectorInterface $collector) {
        if ($collector->getName() === '__meta') {
            throw new CDebug_Bar_Exception("'__meta' is a reserved name and cannot be used as a collector name");
        }
        if (isset($this->collectors[$collector->getName()])) {
            throw new CDebug_Bar_Exception("'{$collector->getName()}' is already a registered collector");
        }
        $this->collectors[$collector->getName()] = $collector;
        return $this;
    }

    /**
     * Returns a data collector
     *
     * @param string $name
     *
     * @return CDebug_Interface_DataCollectorInterface
     *
     * @throws CDebug_Bar_Exception
     */
    public function getCollector($name) {
        if (!isset($this->collectors[$name])) {
            throw new CDebug_Bar_Exception("'$name' is not a registered collector");
        }
        return $this->collectors[$name];
    }

    /**
     * Returns an array of all data collectors
     *
     * @return CDebug_Interface_DataCollectorInterface[]
     */
    public function getCollectors() {
        return $this->collectors;
    }

    /**
     * Checks if a data collector has been added
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasCollector($name) {
        return isset($this->collectors[$name]);
    }

    /**
     * Sets the request id generator
     *
     * @param RequestIdGeneratorInterface $generator
     *
     * @return $this
     */
    public function setRequestIdGenerator(RequestIdGeneratorInterface $generator) {
        $this->requestIdGenerator = $generator;
        return $this;
    }

    /**
     * @return RequestIdGeneratorInterface
     */
    public function getRequestIdGenerator() {
        if ($this->requestIdGenerator === null) {
            $this->requestIdGenerator = new CDebug_Bar_RequestIdGenerator();
        }
        return $this->requestIdGenerator;
    }

    /**
     * Returns the id of the current request
     *
     * @return string
     */
    public function getCurrentRequestId() {
        if ($this->requestId === null) {
            $this->requestId = $this->getRequestIdGenerator()->generate();
        }
        return $this->requestId;
    }

    /**
     * Returns an array of HTTP headers containing the data
     *
     * @param string  $headerName
     * @param integer $maxHeaderLength
     * @param mixed   $maxTotalHeaderLength
     *
     * @return array
     */
    public function getDataAsHeaders($headerName = 'phpdebugbar', $maxHeaderLength = 4096, $maxTotalHeaderLength = 250000) {
        $data = rawurlencode(json_encode([
            'id' => $this->getCurrentRequestId(),
            'data' => $this->getData()
        ]));

        if (strlen($data) > $maxTotalHeaderLength) {
            $data = rawurlencode(json_encode([
                'error' => 'Maximum header size exceeded'
            ]));
        }
        $chunks = [];
        while (strlen($data) > $maxHeaderLength) {
            $chunks[] = substr($data, 0, $maxHeaderLength);
            $data = substr($data, $maxHeaderLength);
        }
        $chunks[] = $data;
        $headers = [];
        for ($i = 0, $c = count($chunks); $i < $c; $i++) {
            $name = $headerName . ($i > 0 ? "-$i" : '');
            $headers[$name] = $chunks[$i];
        }
        return $headers;
    }

    /**
     * Sends the data through the HTTP headers
     *
     * @param bool    $useOpenHandler
     * @param string  $headerName
     * @param integer $maxHeaderLength
     *
     * @return $this
     */
    public function sendDataInHeaders($useOpenHandler = null, $headerName = 'phpdebugbar', $maxHeaderLength = 4096) {
        if ($useOpenHandler === null) {
            $useOpenHandler = self::$useOpenHandlerWhenSendingDataHeaders;
        }
        if ($useOpenHandler && $this->storage !== null) {
            $this->getData();
            $headerName .= '-id';
            $headers = [$headerName => $this->getCurrentRequestId()];
        } else {
            $headers = $this->getDataAsHeaders($headerName, $maxHeaderLength);
        }

        $this->getHttpDriver()->setHeaders($headers);
        return $this;
    }

    /**
     * Stacks the data in the session for later rendering
     */
    public function stackData() {
        $http = $this->initStackSession();
        $data = null;
        if (!$this->isDataPersisted() || $this->stackAlwaysUseSessionStorage) {
            $data = $this->getData();
        } elseif ($this->data === null) {
            $this->collect();
        }
        $stack = $http->getSessionValue($this->stackSessionNamespace);
        $stack[$this->getCurrentRequestId()] = $data;
        $http->setSessionValue($this->stackSessionNamespace, $stack);
        return $this;
    }

    /**
     * Checks if there is stacked data in the session
     *
     * @return boolean
     */
    public function hasStackedData() {
        try {
            $http = $this->initStackSession();
        } catch (CDebug_Bar_Exception $e) {
            return false;
        }
        return count($http->getSessionValue($this->stackSessionNamespace)) > 0;
    }

    /**
     * Returns the data stacked in the session
     *
     * @param boolean $delete Whether to delete the data in the session
     *
     * @return array
     */
    public function getStackedData($delete = true) {
        $http = $this->initStackSession();
        $stackedData = $http->getSessionValue($this->stackSessionNamespace);
        if ($delete) {
            $http->deleteSessionValue($this->stackSessionNamespace);
        }
        $datasets = [];
        if ($this->isDataPersisted() && !$this->stackAlwaysUseSessionStorage) {
            foreach ($stackedData as $id => $data) {
                $datasets[$id] = $this->getStorage()->get($id);
            }
        } else {
            $datasets = $stackedData;
        }
        return $datasets;
    }

    /**
     * Sets the key to use in the $_SESSION array
     *
     * @param string $ns
     *
     * @return $this
     */
    public function setStackDataSessionNamespace($ns) {
        $this->stackSessionNamespace = $ns;
        return $this;
    }

    /**
     * Returns the key used in the $_SESSION array
     *
     * @return string
     */
    public function getStackDataSessionNamespace() {
        return $this->stackSessionNamespace;
    }

    /**
     * Sets whether to only use the session to store stacked data even
     * if a storage is enabled
     *
     * @param boolean $enabled
     *
     * @return $this
     */
    public function setStackAlwaysUseSessionStorage($enabled = true) {
        $this->stackAlwaysUseSessionStorage = $enabled;
        return $this;
    }

    /**
     * Checks if the session is always used to store stacked data
     * even if a storage is enabled
     *
     * @return boolean
     */
    public function isStackAlwaysUseSessionStorage() {
        return $this->stackAlwaysUseSessionStorage;
    }

    /**
     * Initializes the session for stacked data
     *
     * @return HttpDriverInterface
     *
     * @throws DebugBarException
     */
    protected function initStackSession() {
        $http = $this->getHttpDriver();
        if (!$http->isSessionStarted()) {
            throw new CDebug_Exception('Session must be started before using stack data in the debug bar');
        }
        if (!$http->hasSessionValue($this->stackSessionNamespace)) {
            $http->setSessionValue($this->stackSessionNamespace, []);
        }
        return $http;
    }

    /**
     * Returns the HTTP driver
     *
     * If no http driver where defined, a PhpHttpDriver is automatically created
     *
     * @return HttpDriverInterface
     */
    public function getHttpDriver() {
        if ($this->httpDriver === null) {
            $this->httpDriver = new CDebug_Bar_PhpHttpDriver();
        }
        return $this->httpDriver;
    }

    /**
     * Collects the data from the collectors
     *
     * @return array
     */
    public function collect() {
        if (php_sapi_name() === 'cli') {
            $ip = gethostname();
            if ($ip) {
                $ip = gethostbyname($ip);
            } else {
                $ip = '127.0.0.1';
            }
            $request_variables = [
                'method' => 'CLI',
                'uri' => isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : null,
                'ip' => $ip
            ];
        } else {
            $request_variables = [
                'method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null,
                'uri' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null,
                'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null
            ];
        }
        $this->data = [
            '__meta' => array_merge(
                [
                    'id' => $this->getCurrentRequestId(),
                    'datetime' => date('Y-m-d H:i:s'),
                    'utime' => microtime(true)
                ],
                $request_variables
            )
        ];
        foreach ($this->collectors as $name => $collector) {
            $this->data[$name] = $collector->collect();
        }
        // Remove all invalid (non UTF-8) characters
        array_walk_recursive($this->data, function (&$item) {
            if (is_string($item) && !mb_check_encoding($item, 'UTF-8')) {
                $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
            }
        });
        if ($this->storage !== null) {
            $this->storage->save($this->getCurrentRequestId(), $this->data);
        }
        return $this->data;
    }

    /**
     * Returns collected data
     *
     * Will collect the data if none have been collected yet
     *
     * @return array
     */
    public function getData() {
        if ($this->data === null) {
            $this->collect();
        }
        return $this->data;
    }

    public function offsetSet($key, $value) {
        throw new CDebug_Bar_Exception('DebugBar[] is read-only');
    }

    public function offsetGet($key) {
        return $this->getCollector($key);
    }

    public function offsetExists($key) {
        return $this->hasCollector($key);
    }

    public function offsetUnset($key) {
        throw new CDebug_Bar_Exception('DebugBar[] is read-only');
    }
}
