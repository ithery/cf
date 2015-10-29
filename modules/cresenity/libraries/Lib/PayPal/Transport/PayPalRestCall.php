<?php

/**
 * Class PayPalRestCall
 *
 * @package PayPal\Transport
 */
class PayPal_Transport_PayPalRestCall
{


    /**
     * Paypal Logger
     *
     * @var PayPalLoggingManager logger interface
     */
    private $logger;

    /**
     * API Context
     *
     * @var ApiContext
     */
    private $apiContext;


    /**
     * Default Constructor
     *
     * @param ApiContext $apiContext
     */
    public function __construct(PayPal_Rest_ApiContext $apiContext)
    {
        $this->apiContext = $apiContext;
        $this->logger = PayPal_Core_PayPalLoggingManager::getInstance(__CLASS__);
    }

    /**
     * @param array  $handlers Array of handlers
     * @param string $path     Resource path relative to base service endpoint
     * @param string $method   HTTP method - one of GET, POST, PUT, DELETE, PATCH etc
     * @param string $data     Request payload
     * @param array  $headers  HTTP headers
     * @return mixed
     * @throws \PayPal\Exception\PayPalConnectionException
     */
    public function execute($handlers = array(), $path, $method, $data = '', $headers = array())
    {

        $config = $this->apiContext->getConfig();
        $httpConfig = new PayPal_Core_PayPalHttpConfig(null, $method, $config);
        $headers = $headers ? $headers : array();
        $httpConfig->setHeaders($headers +
            array(
                'Content-Type' => 'application/json'
            )
        );

        /** @var \Paypal\Handler\IPayPalHandler $handler */
        foreach ($handlers as $handler) {
            if (!is_object($handler)) {
                //$fullHandler = "\\" . (string)$handler;
				$fullHandler = (string)$handler;
				$handler = new $fullHandler($this->apiContext);
            }
            $handler->handle($httpConfig, $data, array('path' => $path, 'apiContext' => $this->apiContext));
        }

        $connection = new PayPal_Core_PayPalHttpConnection($httpConfig, $config);
        $response = $connection->execute($data);

        return $response;
    }

}
