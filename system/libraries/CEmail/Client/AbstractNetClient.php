<?php

abstract class CEmail_Client_AbstractNetClient {
    /**
     * @var array
     */
    protected $config;

    /**
     * @var bool
     */
    protected $isAutoLogout;

    /**
     * @var resource
     */
    protected $connection;

    /**
     * @var bool
     */
    protected $haveUnreadBuffer;

    /**
     * @var bool
     */
    protected $isRunningCallback;

    /**
     * @var string
     */
    protected $responseBuffer;

    /**
     * @var int
     */
    protected $securityType;

    /**
     * @var string
     */
    protected $connectedHost;

    /**
     * @var int
     */
    protected $connectedPort;

    /**
     * @var bool
     */
    protected $isSecure;

    /**
     * @var int
     */
    protected $connectTimeOut;

    /**
     * @var int
     */
    protected $socketTimeOut;

    /**
     * @var int
     */
    protected $startConnectTime;

    /**
     * @var object
     */
    protected $logger;

    /**
     * @var CEmail_Client_Loader
     */
    protected $loader;

    protected function __construct($config = []) {
        $this->loader = new CEmail_Client_Loader();
        $this->config = $config;
        $this->connection = null;
        $this->haveUnreadBuffer = false;
        $this->isRunningCallback = false;
        $this->logger = null;

        $this->isAutoLogout = true;

        $this->responseBuffer = '';

        $this->securityType = \CEmail_Client::SECURITY_TYPE_NONE;
        $this->connectedHost = '';
        $this->connectedPort = 0;

        $this->isSecure = false;

        $this->connectTimeOut = 10;
        $this->socketTimeOut = 10;

        $this->clear();
    }

    public function getConfig($key, $default = null) {
        return carr::get($this->config, $key, $default);
    }

    /**
     * @return void
     */
    public function __destruct() {
        try {
            if ($this->isAutoLogout) {
                $this->logoutAndDisconnect();
            } else {
                $this->disconnect();
            }
        } catch (\Exception $oException) {
        }
    }

    /**
     * @return void
     */
    public function clear() {
        $this->responseBuffer = '';

        $this->connectedHost = '';
        $this->connectedPort = 0;

        $this->startConnectTime = 0;
        $this->isSecure = false;
    }

    /**
     * @return string
     */
    public function getConnectedHost() {
        return $this->connectedHost;
    }

    /**
     * @return int
     */
    public function getConnectedPort() {
        return $this->connectedPort;
    }

    /**
     * @param int $iConnectTimeOut = 10
     * @param int $iSocketTimeOut  = 10
     *
     * @return void
     */
    public function setTimeOuts($iConnectTimeOut = 10, $iSocketTimeOut = 10) {
        $this->connectTimeOut = $iConnectTimeOut;
        $this->socketTimeOut = $iSocketTimeOut;
    }

    /**
     * @return null|resource
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * @param string $sServerName
     * @param int    $iPort
     * @param int    $iSecurityType = \CEmail_Client::SECURITY_TYPE_AUTO_DETECT
     * @param bool   $verifySsl     = false
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception_SocketAlreadyConnectedException
     * @throws \CEmail_Client_Exception_SocketCanNotConnectToHostException
     *
     * @return void
     */
    public function connect(
        $sServerName,
        $iPort,
        $iSecurityType = \CEmail_Client::SECURITY_TYPE_AUTO_DETECT,
        $verifySsl = false
    ) {
        if (!\CBase_Validation::notEmptyString($sServerName, true) || !\CBase_Validation::isPort($iPort)) {
            $this->writeLogException(
                new \CEmail_Client_Exception_InvalidArgumentException(),
                CLogger::ERROR,
                true
            );
        }

        if ($this->isConnected()) {
            $this->writeLogException(
                new CEmail_Client_Exception_SocketAlreadyConnectedException(),
                CLogger::ERROR,
                true
            );
        }

        $sServerName = \trim($sServerName);

        $errMessage = '';
        $errCode = 0;

        $this->connectedHost = $sServerName;
        $this->connectedPort = $iPort;
        $this->securityType = $iSecurityType;
        $this->isSecure = $this->useSSL(
            $this->connectedPort,
            $this->securityType
        );

        $this->connectedHost = \in_array(\strtolower(\substr($this->connectedHost, 0, 6)), ['ssl://', 'tcp://'])
            ? \substr($this->connectedHost, 6) : $this->connectedHost;

        $this->connectedHost = ($this->isSecure ? 'ssl://' : 'tcp://') . $this->connectedHost;
        //$this->connectedHost = ($this->bSecure ? 'ssl://' : '').$this->connectedHost;

        if (!$this->isSecure && \CEmail_Client::SECURITY_TYPE_SSL === $this->securityType) {
            $this->writeLogException(
                new \CEmail_Client_Exception_SocketUnsupportedSecureConnectionException('SSL isn\'t supported: (' . \implode(', ', \stream_get_transports()) . ')'),
                \CLogger::ERROR,
                true
            );
        }

        $this->startConnectTime = \microtime(true);
        $this->writeLog(
            'Start connection to "' . $this->connectedHost . ':' . $this->iConnectedPort . '"',
            \CLogger::INFO
        );

        $verifySsl = !!$verifySsl;
        $streamContextSettings = [
            'ssl' => [
                'verify_host' => $verifySsl,
                'verify_peer' => $verifySsl,
                'verify_peer_name' => $verifySsl,
                'allow_self_signed' => !$verifySsl
            ]
        ];

        //\MailSo\Hooks::Run('Net.NetClient.StreamContextSettings/Filter', [&$aStreamContextSettings]);

        $streamContext = \stream_context_create($streamContextSettings);

        \set_error_handler([&$this, 'capturePhpErrorWithException']);

        try {
            $this->connection = \stream_socket_client(
                $this->connectedHost . ':' . $this->connectedPort,
                $errCode,
                $errMessage,
                $this->connectTimeOut,
                STREAM_CLIENT_CONNECT,
                $streamContext
            );
        } catch (\Exception $oExc) {
            $errMessage = $oExc->getMessage();
            $errCode = $oExc->getCode();
        }

        \restore_error_handler();

        if (!\is_resource($this->connection)) {
            $this->writeLogException(
                new CEmail_Client_Exception_SocketCanNotConnectToHostException(
                    cstr::ascii($errMessage),
                    (int) $errCode,
                    'Can\'t connect to host "' . $this->connectedHost . ':' . $this->connectedPort . '"'
                ),
                \CLogger::NOTICE,
                true
            );
        }

        $this->writeLog(
            (\microtime(true) - $this->iStartConnectTime) . ' (raw connection)',
            \CLogger::INFO
        );

        if ($this->connection) {
            if (\CBase_DependencyUtils::functionExistsAndEnabled('stream_set_timeout')) {
                @\stream_set_timeout($this->connection, $this->iSocketTimeOut);
            }
        }
    }

    /**
     * @param {int} $iCryptoType = STREAM_CRYPTO_METHOD_TLS_CLIENT
     */
    public function enableCrypto($iCryptoType = STREAM_CRYPTO_METHOD_TLS_CLIENT) {
        if (\is_resource($this->connection)
            && \CBase_DependencyUtils::functionExistsAndEnabled('stream_socket_enable_crypto')
        ) {
            if (!@\stream_socket_enable_crypto($this->connection, true, $iCryptoType)) {
                $this->writeLogException(
                    new \CEmail_Client_Exception('Cannot enable STARTTLS. [type=' . $iCryptoType . ']'),
                    \CLogger::ERROR,
                    true
                );
            }
        }
    }

    /**
     * @return void
     */
    public function disconnect() {
        if (\is_resource($this->connection)) {
            $bResult = \fclose($this->connection);

            $this->writeLog('Disconnected from "' . $this->connectedHost . ':' . $this->iConnectedPort . '" ('
                . (($bResult) ? 'success' : 'unsuccess') . ')', \CLogger::INFO);

            if (0 !== $this->iStartConnectTime) {
                $this->writeLog(
                    (\microtime(true) - $this->iStartConnectTime) . ' (net session)',
                    \CLogger::INFO
                );

                $this->iStartConnectTime = 0;
            }

            $this->connection = null;
        }
    }

    /**
     * @retun void
     *
     * @throws \CEmail_Client_Exception
     */
    public function logoutAndDisconnect() {
        if (\method_exists($this, 'logout') && !$this->haveUnreadBuffer && !$this->isRunningCallback) {
            $this->logout();
        }

        $this->disconnect();
    }

    /**
     * @param bool $bThrowExceptionOnFalse = false
     *
     * @return bool
     */
    public function isConnected($bThrowExceptionOnFalse = false) {
        $bResult = \is_resource($this->connection);
        if (!$bResult && $bThrowExceptionOnFalse) {
            $this->writeLogException(
                new CEmail_Client_Exception_SocketConnectionDoesNotAvailableException(),
                \CLogger::ERROR,
                true
            );
        }

        return $bResult;
    }

    /**
     * @throws \CEmail_Client_Exception_SocketConnectionDoesNotAvailableException
     *
     * @return void
     */
    public function isConnectedWithException() {
        $this->IsConnected(true);
    }

    /**
     * @return array|bool
     */
    public function streamContextParams() {
        return \is_resource($this->connection) && \CBase_DependencyUtils::functionExistsAndEnabled('stream_context_get_options')
            ? \stream_context_get_params($this->connection) : false;
    }

    /**
     * @param string $sRaw
     * @param bool   $bWriteToLog = true
     * @param string $sFakeRaw    = ''
     *
     * @throws \CEmail_Client_Exception_SocketConnectionDoesNotAvailableException
     * @throws \CEmail_Client_Exception_SocketWriteException
     *
     * @return void
     */
    protected function sendRaw($sRaw, $bWriteToLog = true, $sFakeRaw = '') {
        if ($this->haveUnreadBuffer) {
            $this->writeLogException(
                new CEmail_Client_Exception_SocketUnreadBufferException(),
                \CLogger::ERROR,
                true
            );
        }

        $bFake = 0 < \strlen($sFakeRaw);
        $sRaw .= "\r\n";

        if ($this->oLogger && $this->oLogger->IsShowSecter()) {
            $bFake = false;
        }

        if ($bFake) {
            $sFakeRaw .= "\r\n";
        }

        $mResult = @\fwrite($this->connection, $sRaw);
        if (false === $mResult) {
            $this->IsConnected(true);

            $this->writeLogException(
                new CEmail_Client_Exception_SocketWriteException(),
                \CLogger::ERROR,
                true
            );
        } else {
            $this->loader->incStatistic('netWrite', $mResult);

            if ($bWriteToLog) {
                $this->writeLogWithCrlf(
                    '> ' . ($bFake ? $sFakeRaw : $sRaw),
                    //.' ['.$iWriteSize.']',
                    $bFake ? \CLogger::DEBUG : CLogger::INFO
                );
            }
        }
    }

    /**
     * @param mixed $mReadLen    = null
     * @param bool  $bForceLogin = false
     *
     * @throws \MailSo\Net\Exceptions\SocketConnectionDoesNotAvailableException
     * @throws \MailSo\Net\Exceptions\SocketReadException
     *
     * @return void
     */
    protected function getNextBuffer($mReadLen = null, $bForceLogin = false) {
        if (null === $mReadLen) {
            $this->sResponseBuffer = @\fgets($this->connection);
        } else {
            $this->sResponseBuffer = '';
            $iRead = $mReadLen;
            while (0 < $iRead) {
                $sAddRead = @\fread($this->connection, $iRead);
                if (false === $sAddRead) {
                    $this->sResponseBuffer = false;

                    break;
                }

                $this->sResponseBuffer .= $sAddRead;
                $iRead -= \strlen($sAddRead);
            }
        }

        if (false === $this->sResponseBuffer) {
            $this->IsConnected(true);
            $this->haveUnreadBuffer = true;

            $aSocketStatus = @\stream_get_meta_data($this->connection);
            if (isset($aSocketStatus['timed_out']) && $aSocketStatus['timed_out']) {
                $this->writeLogException(
                    new CEmail_Client_Exception_SocketReadTimeoutException(),
                    \CLogger::ERROR,
                    true
                );
            } else {
                //$this->writeLog('Stream Meta: '.
                //\print_r($aSocketStatus, true), \MailSo\Log\Enumerations\Type::ERROR);
                $this->writeLogException(
                    new CEmail_Client_Exception_SocketReadException(),
                    \CLogger::ERROR,
                    true
                );
            }
        } else {
            $iReadedLen = \strlen($this->sResponseBuffer);
            if (null === $mReadLen || $bForceLogin) {
                $iLimit = 5000; // 5kb
                if ($iLimit < $iReadedLen) {
                    $this->writeLogWithCrlf(
                        '[cutted:' . $iReadedLen . 'b] < ' . \substr($this->sResponseBuffer . '...', 0, $iLimit),
                        CLogger::INFO
                    );
                } else {
                    $this->writeLogWithCrlf(
                        '< ' . $this->sResponseBuffer,
                        //.' ['.$iReadedLen.']',
                        CLogger::INFO
                    );
                }
            } else {
                $this->writeLog(
                    'Received ' . $iReadedLen . '/' . $mReadLen . ' bytes.',
                    \CLogger::INFO
                );
            }

            $this->loader->incStatistic('netRead', $iReadedLen);
        }
    }

    /**
     * @return string
     */
    protected function getLogName() {
        return 'NET';
    }

    /**
     * @param string $sDesc
     * @param int    $iDescType = \MailSo\Log\Enumerations\Type::INFO
     *
     * @return void
     */
    protected function writeLog($sDesc, $iDescType = \CLogger::INFO) {
        if ($this->oLogger) {
            $this->oLogger->Write($sDesc, $iDescType, $this->getLogName());
        }
    }

    /**
     * @param string $sDesc
     * @param int    $iDescType = \MailSo\Log\Enumerations\Type::INFO
     *
     * @return void
     */
    protected function writeLogWithCrlf($sDesc, $iDescType = CLogger::INFO) {
        $this->writeLog(\strtr($sDesc, ["\r" => '\r', "\n" => '\n']), $iDescType);
    }

    /**
     * @param \Exception $oException
     * @param int        $iDescType       = \MailSo\Log\Enumerations\Type::NOTICE
     * @param bool       $bThrowException = false
     *
     * @return void
     */
    protected function writeLogException(
        $oException,
        $iDescType = CLogger::NOTICE,
        $bThrowException = false
    ) {
        if ($this->logger) {
            if ($oException instanceof CEmail_Client_Exception_SocketCanNotConnectToHostException) {
                $this->logger->write('Socket: [' . $oException->getSocketCode() . '] ' . $oException->getSocketMessage(), $iDescType, $this->getLogName());
            }

            $this->logger->writeException($oException, $iDescType, $this->getLogName());
        }

        if ($bThrowException) {
            throw $oException;
        }
    }

    /**
     * @param object $oLogger
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     *
     * @return void
     */
    public function setLogger($oLogger) {
        // if (!($oLogger instanceof \MailSo\Log\Logger)) {
        //     throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        // }

        $this->logger = $oLogger;
    }

    /**
     * @return null|object
     */
    public function logger() {
        return $this->oLogger;
    }

    /**
     * @param int $iPort
     * @param int $iSecurityType
     *
     * @return bool
     */
    public function useSSL($iPort, $iSecurityType) {
        $iPort = (int) $iPort;
        $iResult = (int) $iSecurityType;
        if (CEmail_Client::SECURITY_TYPE_AUTO_DETECT === $iSecurityType) {
            switch (true) {
                case 993 === $iPort:
                case 995 === $iPort:
                case 465 === $iPort:
                    $iResult = CEmail_Client::SECURITY_TYPE_SSL;

                    break;
            }
        }

        if (CEmail_Client::SECURITY_TYPE_SSL === $iResult && !\in_array('ssl', \stream_get_transports())) {
            $iResult = CEmail_Client::SECURITY_TYPE_NONE;
        }

        return CEmail_Client::SECURITY_TYPE_SSL === $iResult;
    }

    /**
     * @param bool $bSupported
     * @param int  $iSecurityType
     * @param bool $bHasSupportedAuth = true
     *
     * @return bool
     */
    public function useStartTLS($bSupported, $iSecurityType, $bHasSupportedAuth = true) {
        return $bSupported
            && (CEmail_Client::SECURITY_TYPE_STARTTLS === $iSecurityType
                || (CEmail_Client::SECURITY_TYPE_AUTO_DETECT === $iSecurityType && (!$bHasSupportedAuth || $this->getConfig('preferStartTlsIfAutoDetect'))))
            && \defined('STREAM_CRYPTO_METHOD_TLS_CLIENT') && CBase_DependencyUtils::FunctionExistsAndEnabled('stream_socket_enable_crypto');
    }
}
