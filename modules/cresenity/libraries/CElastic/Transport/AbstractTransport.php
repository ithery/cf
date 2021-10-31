<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 8:40:52 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CElastic_Transport_AbstractTransport extends CElastic_Param {

    /**
     * @var CElastic_Connection
     */
    protected $_connection;

    /**
     * Construct transport.
     *
     * @param \Elastica\Connection $connection Connection object
     */
    public function __construct(Connection $connection = null) {
        if ($connection) {
            $this->setConnection($connection);
        }
    }

    /**
     * @return CElastic_Connection Connection object
     */
    public function getConnection() {
        return $this->_connection;
    }

    /**
     * @param CElastic_Connection $connection Connection object
     *
     * @return $this
     */
    public function setConnection(CElastic_Connection $connection) {
        $this->_connection = $connection;
        return $this;
    }

    /**
     * Executes the transport request.
     *
     * @param CElastic_Client_Request   $request Request object
     * @param array                     $params  Hostname, port, path, ...
     *
     * @return CElastic_Client_Response Response object
     */
    abstract public function exec(CElastic_Client_Request $request, array $params);

    /**
     * BOOL values true|false should be sanityzed and passed to Elasticsearch
     * as string.
     *
     * @param string $query
     *
     * @return mixed
     */
    public function sanityzeQueryStringBool($query) {
        foreach ($query as $key => $value) {
            if (is_bool($value)) {
                $query[$key] = ($value) ? 'true' : 'false';
            }
        }
        return $query;
    }

    /**
     * Create a transport.
     *
     * The $transport parameter can be one of the following values:
     *
     * * string: The short name of a transport. For instance "Http"
     * * object: An already instantiated instance of a transport
     * * array: An array with a "type" key which must be set to one of the two options. All other
     *          keys in the array will be set as parameters in the transport instance
     *
     * @param mixed                $transport  A transport definition
     * @param CElastic_Connection $connection A connection instance
     * @param array                $params     Parameters for the transport class
     *
     * @throws CElastic_Exception_InvalidException
     *
     * @return CElastic_Transport_AbstractTransport
     */
    public static function create($transport, CElastic_Connection $connection, array $params = []) {
        if (is_array($transport) && isset($transport['type'])) {
            $transportParams = $transport;
            unset($transportParams['type']);
            $params = array_replace($params, $transportParams);
            $transport = $transport['type'];
        }
        if (is_string($transport)) {
            $specialTransports = [
                'httpadapter' => 'HttpAdapter',
                'nulltransport' => 'NullTransport',
            ];
            if (isset($specialTransports[strtolower($transport)])) {
                $transport = $specialTransports[strtolower($transport)];
            } else {
                $transport = ucfirst($transport);
            }
            $classNames = ["CElastic_Transport_" . $transport, $transport];
            foreach ($classNames as $className) {
                if (class_exists($className)) {
                    $transport = new $className();
                    break;
                }
            }
        }
        if ($transport instanceof self) {
            $transport->setConnection($connection);
            foreach ($params as $key => $value) {
                $transport->setParam($key, $value);
            }
        } else {
            throw new CElastic_Exception_InvalidException('Invalid transport ' . $transport);
        }
        return $transport;
    }

}
