<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 8:45:11 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Elastica Http Transport object.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 */
class CElastic_Transport_Http extends CElastic_Transport_AbstractTransport {

    /**
     * Http scheme.
     *
     * @var string Http scheme
     */
    protected $_scheme = 'http';

    /**
     * Curl resource to reuse.
     *
     * @var resource Curl resource to reuse
     */
    protected static $_curlConnection;

    /**
     * Makes calls to the elasticsearch server.
     *
     * All calls that are made to the server are done through this function
     *
     * @param CElastic_Client_Request $request
     * @param array             $params  Host, Port, ...
     *
     * @throws CElastic_Exception_ConnectionException
     * @throws CElastic_Exception_ResponseException
     * @throws CElastic_Exception_Connection_HttpException
     *
     * @return CElastic_Client_Response Response object
     */
    public function exec(CElastic_Client_Request $request, array $params) {
        $connection = $this->getConnection();
        $conn = $this->_getConnection($connection->isPersistent());
        // If url is set, url is taken. Otherwise port, host and path
        $url = $connection->hasConfig('url') ? $connection->getConfig('url') : '';
        if (!empty($url)) {
            $baseUri = $url;
        } else {
            $baseUri = $this->_scheme . '://' . $connection->getHost() . ':' . $connection->getPort() . '/' . $connection->getPath();
        }
        $requestPath = $request->getPath();
        if (!CElastic_Util::isDateMathEscaped($requestPath)) {
            $requestPath = CElastic_Util::escapeDateMath($requestPath);
        }
        $baseUri .= $requestPath;
        $query = $request->getQuery();
        if (!empty($query)) {
            $baseUri .= '?' . http_build_query(
                            $this->sanityzeQueryStringBool($query)
            );
        }
        curl_setopt($conn, CURLOPT_URL, $baseUri);
        
        curl_setopt($conn, CURLOPT_TIMEOUT, $connection->getTimeout());
        curl_setopt($conn, CURLOPT_FORBID_REUSE, 0);
        // Tell ES that we support the compressed responses
        // An "Accept-Encoding" header containing all supported encoding types is sent
        // curl will decode the response automatically if the response is encoded
        curl_setopt($conn, CURLOPT_ENCODING, '');
        /* @see Connection::setConnectTimeout() */
        $connectTimeout = $connection->getConnectTimeout();
        if ($connectTimeout > 0) {
            curl_setopt($conn, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        }
        $proxy = $connection->getProxy();
        // See: https://github.com/facebook/hhvm/issues/4875
        if (is_null($proxy) && defined('HHVM_VERSION')) {
            $proxy = getenv('http_proxy') ?: null;
        }
        if (!is_null($proxy)) {
            curl_setopt($conn, CURLOPT_PROXY, $proxy);
        }
        $username = $connection->getUsername();
        $password = $connection->getPassword();
        if (!is_null($username) && !is_null($password)) {
            curl_setopt($conn, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($conn, CURLOPT_USERPWD, "$username:$password");
        }
        $this->_setupCurl($conn);
        $headersConfig = $connection->hasConfig('headers') ? $connection->getConfig('headers') : [];
        $headers = [];
        if (!empty($headersConfig)) {
            $headers = [];
            foreach ($headersConfig as $header => $headerValue) {
                array_push($headers, $header . ': ' . $headerValue);
            }
        }
        // TODO: REFACTOR
        $data = $request->getData();
        
        $httpMethod = $request->getMethod();
        $content='';
        
        if (!empty($data) || '0' === $data) {
            if ($this->hasParam('postWithRequestBody') && $this->getParam('postWithRequestBody') == true) {
                $httpMethod = CElastic_Client_Request::POST;
            }
            if (is_array($data)) {
                $content = CHelper::json()->stringify($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                
            } else {
                $content = $data;
                // Escaping of / not necessary. Causes problems in base64 encoding of files
                $content = str_replace('\/', '/', $content);
            }
            
            array_push($headers, sprintf('Content-Type: %s', $request->getContentType()));
            if ($connection->hasCompression()) {
                // Compress the body of the request ...
                curl_setopt($conn, CURLOPT_POSTFIELDS, gzencode($content));
                // ... and tell ES that it is compressed
                array_push($headers, 'Content-Encoding: gzip');
            } else {

                curl_setopt($conn, CURLOPT_POSTFIELDS, $content);
            }
        } else {
            curl_setopt($conn, CURLOPT_POSTFIELDS, '');
        }
        curl_setopt($conn, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($conn, CURLOPT_NOBODY, $httpMethod == 'HEAD');
        curl_setopt($conn, CURLOPT_CUSTOMREQUEST, $httpMethod);
        $start = microtime(true);
        // cURL opt returntransfer leaks memory, therefore OB instead.
        ob_start();
        curl_exec($conn);
        $responseString = ob_get_clean();
        $end = microtime(true);
        // Checks if error exists
        $errorNumber = curl_errno($conn);
        $response = new CElastic_Client_Response($responseString, curl_getinfo($conn, CURLINFO_HTTP_CODE));
        $response->setQueryTime($end - $start);
        $response->setTransferInfo(curl_getinfo($conn));
        if ($connection->hasConfig('bigintConversion')) {
            $response->setJsonBigintConversion($connection->getConfig('bigintConversion'));
        }
        if ($response->hasError()) {
            throw new CElastic_Exception_ResponseException($request, $response);
        }
        if ($response->hasFailedShards()) {
            throw new CElastic_Exception_PartialShardFailureException($request, $response);
        }
        if ($errorNumber > 0) {
            throw new CElastic_Exception_Connection_HttpException($errorNumber, $request, $response);
        }
        return $response;
    }

    /**
     * Called to add additional curl params.
     *
     * @param resource $curlConnection Curl connection
     */
    protected function _setupCurl($curlConnection) {
        if ($this->getConnection()->hasConfig('curl')) {
            foreach ($this->getConnection()->getConfig('curl') as $key => $param) {
                curl_setopt($curlConnection, $key, $param);
            }
        }
    }

    /**
     * Return Curl resource.
     *
     * @param bool $persistent False if not persistent connection
     *
     * @return resource Connection resource
     */
    protected function _getConnection($persistent = true) {
        if (!$persistent || !self::$_curlConnection) {
            self::$_curlConnection = curl_init();
        }
        return self::$_curlConnection;
    }

}
