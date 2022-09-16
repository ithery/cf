<?php

class CEmail_Client_ImapClient extends \CEmail_Client_AbstractNetClient {
    use CEmail_Client_Imap_Trait_ImapClientMessageTrait;

    /**
     * @var string
     */
    const TAG_PREFIX = 'TAG';

    /**
     * @var bool
     */
    public $forceSelectOnExamine;

    /**
     * @var int
     */
    private $iResponseBufParsedPos;

    /**
     * @var int
     */
    private $iTagCount;

    /**
     * @var array
     */
    private $aCapabilityItems;

    /**
     * @var \CEmail_Client\Imap\FolderInformation
     */
    private $oCurrentFolderInfo;

    /**
     * @var array
     */
    private $aLastResponse;

    /**
     * @var array
     */
    private $aFetchCallbacks;

    /**
     * @var bool
     */
    private $bNeedNext;

    /**
     * @var array
     */
    private $aPartialResponses;

    /**
     * @var array
     */
    private $aTagTimeouts;

    /**
     * @var bool
     */
    private $isLoggined;

    /**
     * @var bool
     */
    private $bIsSelected;

    /**
     * @var string
     */
    private $sLogginedUser;

    protected function __construct() {
        parent::__construct();

        $this->iTagCount = 0;
        $this->aCapabilityItems = null;
        $this->oCurrentFolderInfo = null;
        $this->aFetchCallbacks = null;
        $this->iResponseBufParsedPos = 0;

        $this->aLastResponse = [];
        $this->bNeedNext = true;
        $this->aPartialResponses = [];

        $this->aTagTimeouts = [];

        $this->isLoggined = false;
        $this->bIsSelected = false;
        $this->sLogginedUser = '';

        $this->__FORCE_SELECT_ON_EXAMINE__ = true;

        @\ini_set('xdebug.max_nesting_level', 500);
    }

    /**
     * @return \CEmail_Client_ImapClient
     */
    public static function newInstance() {
        return new self();
    }

    /**
     * @return string
     */
    public function getLogginedUser() {
        return $this->sLogginedUser;
    }

    /**
     * @param string $sServerName
     * @param int    $iPort         = 143
     * @param int    $iSecurityType = \CEmail_Client\Net\Enumerations\ConnectionSecurityType::AUTO_DETECT
     * @param bool   $bVerifySsl    = false
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function connect(
        $sServerName,
        $iPort = 143,
        $iSecurityType = \CEmail_Client::SECURITY_TYPE_AUTO_DETECT,
        $bVerifySsl = false
    ) {
        $this->aTagTimeouts['*'] = \microtime(true);

        parent::connect($sServerName, $iPort, $iSecurityType, $bVerifySsl);

        $this->parseResponseWithValidation('*', true);

        if ($this->useStartTLS($this->isSupported('STARTTLS'), $this->securityType)) {
            $this->sendRequestWithCheck('STARTTLS');
            $this->enableCrypto();

            $this->aCapabilityItems = null;
        } elseif (\CEmail_Client::SECURITY_TYPE_STARTTLS === $this->securityType) {
            $this->writeLogException(
                new \CEmail_Client_Exception_SocketUnsupportedSecureConnectionException('STARTTLS is not supported'),
                \CLogger::ERROR,
                true
            );
        }

        return $this;
    }

    /**
     * @param string $sLogin
     * @param string $sPassword
     * @param string $sProxyAuthUser           = ''
     * @param bool   $bUseAuthPlainIfSupported = false
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function login($sLogin, $sPassword, $sProxyAuthUser = '', $bUseAuthPlainIfSupported = false) {
        if (!\CBase_Validation::notEmptyString($sLogin, true)
            || !\CBase_Validation::notEmptyString($sPassword, true)
        ) {
            $this->writeLogException(
                new \CEmail_Client_Exception_InvalidArgumentException('Can\'t login with empty password'),
                \CLogger::ERROR,
                true
            );
        }

        $sLogin = \trim($sLogin);
        $sLogin = \cstr::ascii($sLogin);
        $sPassword = $sPassword;

        $this->sLogginedUser = $sLogin;

        try {
            if (false && $this->isSupported('AUTH=CRAM-MD5')) {
                $this->sendRequest('AUTHENTICATE', ['CRAM-MD5']);

                $aResponse = $this->parseResponseWithValidation();
                if ($aResponse && \is_array($aResponse) && 0 < \count($aResponse)
                    && \CEmail_Client_Imap_Response::RESPONSE_TYPE_CONTINUATION === $aResponse[\count($aResponse) - 1]->responseType
                ) {
                    $oContinuationResponse = null;
                    foreach ($aResponse as $oResponse) {
                        if ($oResponse && \CEmail_Client_Imap_Response::RESPONSE_TYPE_CONTINUATION === $oResponse->responseType) {
                            $oContinuationResponse = $oResponse;
                        }
                    }

                    if ($oContinuationResponse) {
                        $sToken = \base64_encode("\0" . $sLogin . "\0" . $sPassword);
                        if ($this->logger) {
                            $this->logger->AddSecret($sToken);
                        }

                        $this->Logger()->WriteDump($aResponse);

                        $this->sendRaw($sToken, true, '*******');
                        $this->parseResponseWithValidation();
                    } else {
                        // TODO
                    }
                }
            } elseif ($bUseAuthPlainIfSupported && $this->isSupported('AUTH=PLAIN')) {
                $sToken = \base64_encode("\0" . $sLogin . "\0" . $sPassword);
                if ($this->logger) {
                    $this->logger->AddSecret($sToken);
                }

                if ($this->isSupported('AUTH=SASL-IR') && false) {
                    $this->sendRequestWithCheck('AUTHENTICATE', ['PLAIN', $sToken]);
                } else {
                    $this->sendRequest('AUTHENTICATE', ['PLAIN']);
                    $this->parseResponseWithValidation();

                    $this->sendRaw($sToken, true, '*******');
                    $this->parseResponseWithValidation();
                }
            } else {
                if ($this->logger) {
                    $this->logger->AddSecret($this->EscapeString($sPassword));
                }

                $this->sendRequestWithCheck(
                    'LOGIN',
                    [
                        $this->EscapeString($sLogin),
                        $this->EscapeString($sPassword)
                    ]
                );
            }

            if (0 < \strlen($sProxyAuthUser)) {
                $this->sendRequestWithCheck('PROXYAUTH', [$this->EscapeString($sProxyAuthUser)]);
            }
        } catch (\CEmail_Client_Imap_Exception_NegativeResponseException $oException) {
            $this->writeLogException(
                new \CEmail_Client_Imap_Exception_LoginBadCredentialsException($oException->getResponses()),
                \CLogger::NOTICE,
                true
            );
        }

        $this->isLoggined = true;
        $this->aCapabilityItems = null;

        return $this;
    }

    public static function getXOAuthKeyStatic($sEmail, $sAccessToken) {
        if ($sEmail == null || empty($sEmail) || $sAccessToken == null || empty($sAccessToken)) {
            throw new \CEmail_Client_Exception_InvalidArgumentException();
        }

        return \base64_encode('user=' . $sEmail . "\1" . 'auth=Bearer ' . $sAccessToken . "\1\1");
    }

    /**
     * @param string $sXOAuth2Token
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function loginWithXOauth2($sXOAuth2Token) {
        if (!\CBase_Validation::notEmptyString($sXOAuth2Token, true)) {
            $this->writeLogException(
                new \CEmail_Client_Exception_InvalidArgumentException(),
                \CLogger::ERROR,
                true
            );
        }

        if (!$this->isSupported('AUTH=XOAUTH2')) {
            $this->writeLogException(
                new \CEmail_Client_Imap_Exception_LoginBadMethodException(),
                \CLogger::NOTICE,
                true
            );
        }

        try {
            $this->sendRequestWithCheck('AUTHENTICATE', ['XOAUTH2', trim($sXOAuth2Token)]);
        } catch (\CEmail_Client_Imap_Exception_NegativeResponseException $oException) {
            $this->writeLogException(
                new \CEmail_Client_Imap_Exception_LoginBadCredentialsException(
                    $oException->GetResponses(),
                    '',
                    0,
                    $oException
                ),
                \CLogger::NOTICE,
                true
            );
        }

        $this->isLoggined = true;
        $this->aCapabilityItems = null;

        return $this;
    }

    /**
     * @throws \CEmail_Client_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function logout() {
        if ($this->isLoggined) {
            $this->isLoggined = false;
            $this->sendRequestWithCheck('LOGOUT', []);
        }

        return $this;
    }

    /**
     * @return \CEmail_Client_ImapClient
     */
    public function forceCloseConnection() {
        $this->Disconnect();

        return $this;
    }

    /**
     * @return bool
     */
    public function isLoggined() {
        return $this->isConnected() && $this->isLoggined;
    }

    /**
     * @return bool
     */
    public function isSelected() {
        return $this->isLoggined() && $this->bIsSelected;
    }

    /**
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return null|array
     */
    public function capability() {
        $this->sendRequestWithCheck('CAPABILITY', [], true);

        return $this->aCapabilityItems;
    }

    /**
     * @param string $sExtentionName
     *
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return bool
     */
    public function isSupported($sExtentionName) {
        $bResult = \CBase_Validation::notEmptyString($sExtentionName, true);
        if ($bResult && null === $this->aCapabilityItems) {
            $this->aCapabilityItems = $this->capability();
        }

        return $bResult && \is_array($this->aCapabilityItems)
            && \in_array(\strtoupper($sExtentionName), $this->aCapabilityItems);
    }

    /**
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return null|\CEmail_Client\Imap\NamespaceResult
     */
    public function getNamespace() {
        if (!$this->isSupported('NAMESPACE')) {
            return null;
        }

        $oReturn = false;

        $this->sendRequest('NAMESPACE');
        $aResult = $this->parseResponseWithValidation();

        $oImapResponse = null;
        foreach ($aResult as /* @var $oImapResponse \CEmail_Client\Imap\Response */ $oImapResponse) {
            if (\CEmail_Client_Imap_Response::RESPONSE_TYPE_UNTAGGED === $oImapResponse->responseType
                && 'NAMESPACE' === $oImapResponse->statusOrIndex
            ) {
                $oReturn = CEmail_Client_Imap_NamespaceResult::newInstance();
                $oReturn->InitByImapResponse($oImapResponse);

                break;
            }
        }

        if (false === $oReturn) {
            $this->writeLogException(
                new \CEmail_Client_Imap_Exception_ResponseException($aResult),
                \CLogger::ERROR,
                true
            );
        }

        return $oReturn;
    }

    /**
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function noop() {
        return $this->sendRequestWithCheck('NOOP');
    }

    /**
     * @param string $sFolderName
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function folderCreate($sFolderName) {
        return $this->sendRequestWithCheck(
            'CREATE',
            [$this->escapeString($sFolderName)]
        );
    }

    /**
     * @param string $sFolderName
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function folderDelete($sFolderName) {
        return $this->sendRequestWithCheck(
            'DELETE',
            [$this->escapeString($sFolderName)]
        );
    }

    /**
     * @param string $sFolderName
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function folderSubscribe($sFolderName) {
        return $this->sendRequestWithCheck(
            'SUBSCRIBE',
            [$this->EscapeString($sFolderName)]
        );
    }

    /**
     * @param string $sFolderName
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function folderUnSubscribe($sFolderName) {
        return $this->sendRequestWithCheck(
            'UNSUBSCRIBE',
            [$this->EscapeString($sFolderName)]
        );
    }

    /**
     * @param string $sOldFolderName
     * @param string $sNewFolderName
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function folderRename($sOldFolderName, $sNewFolderName) {
        return $this->sendRequestWithCheck('RENAME', [
            $this->EscapeString($sOldFolderName),
            $this->EscapeString($sNewFolderName)]);
    }

    /**
     * @param array $aResult
     *
     * @return array
     */
    protected function getStatusFolderInformation($aResult) {
        $aReturn = [];

        if (\is_array($aResult)) {
            $oImapResponse = null;
            foreach ($aResult as /* @var $oImapResponse \CEmail_Client\Imap\Response */ $oImapResponse) {
                if (\CEmail_Client_Imap_Response::RESPONSE_TYPE_UNTAGGED === $oImapResponse->responseType
                    && 'STATUS' === $oImapResponse->statusOrIndex && isset($oImapResponse->responseList[3])
                    && \is_array($oImapResponse->responseList[3])
                ) {
                    $sName = null;
                    foreach ($oImapResponse->responseList[3] as $sArrayItem) {
                        if (null === $sName) {
                            $sName = $sArrayItem;
                        } else {
                            $aReturn[$sName] = $sArrayItem;
                            $sName = null;
                        }
                    }
                }
            }
        }

        return $aReturn;
    }

    /**
     * @param string $sFolderName
     * @param array  $aStatusItems
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return array|bool
     */
    public function folderStatus($sFolderName, array $aStatusItems) {
        $aResult = false;
        if (\count($aStatusItems) > 0) {
            $this->sendRequest(
                'STATUS',
                [$this->EscapeString($sFolderName), $aStatusItems]
            );

            $aResult = $this->getStatusFolderInformation(
                $this->parseResponseWithValidation()
            );
        }

        return $aResult;
    }

    /**
     * @param array $aResult
     * @param mixed $sStatus
     * @param mixed $bUseListStatus
     *
     * @return array
     */
    private function getFoldersFromResult(array $aResult, $sStatus, $bUseListStatus = false) {
        $aReturn = [];

        $oImapResponse = null;
        foreach ($aResult as /* @var $oImapResponse \CEmail_Client\Imap\Response */ $oImapResponse) {
            if (\CEmail_Client_Imap_Response::RESPONSE_TYPE_UNTAGGED === $oImapResponse->responseType
                && $sStatus === $oImapResponse->statusOrIndex && 5 === count($oImapResponse->responseList)
            ) {
                try {
                    $oFolder = CEmail_Client_Imap_Folder::newInstance(
                        $oImapResponse->responseList[4],
                        $oImapResponse->responseList[3],
                        $oImapResponse->responseList[2]
                    );

                    $aReturn[] = $oFolder;
                } catch (\CEmail_Client_Exception_InvalidArgumentException $oException) {
                    $this->writeLogException($oException, \CLogger::WARNING, false);
                }
            }
        }

        if ($bUseListStatus) {
            foreach ($aResult as /* @var $oImapResponse \CEmail_Client\Imap\Response */ $oImapResponse) {
                if (\CEmail_Client_Imap_Response::RESPONSE_TYPE_UNTAGGED === $oImapResponse->responseType
                    && 'STATUS' === $oImapResponse->statusOrIndex
                    && isset($oImapResponse->responseList[2], $oImapResponse->responseList[3])
                    && \is_array($oImapResponse->responseList[3])
                ) {
                    $sFolderNameRaw = $oImapResponse->responseList[2];

                    $oCurrentFolder = null;
                    foreach ($aReturn as &$oFolder) {
                        if ($oFolder && $sFolderNameRaw === $oFolder->FullNameRaw()) {
                            $oCurrentFolder = &$oFolder;

                            break;
                        }
                    }

                    if (null !== $oCurrentFolder) {
                        $sName = null;
                        $aStatus = [];
                        foreach ($oImapResponse->responseList[3] as $sArrayItem) {
                            if (null === $sName) {
                                $sName = $sArrayItem;
                            } else {
                                $aStatus[$sName] = $sArrayItem;
                                $sName = null;
                            }
                        }

                        if (0 < count($aStatus)) {
                            $oCurrentFolder->setExtended('STATUS', $aStatus);
                        }
                    }

                    unset($oCurrentFolder);
                }
            }
        }

        return $aReturn;
    }

    /**
     * @param bool   $bIsSubscribeList
     * @param string $sParentFolderName = ''
     * @param string $sListPattern      = '*'
     * @param bool   $bUseListStatus    = false
     *
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return array
     */
    private function specificFolderList($bIsSubscribeList, $sParentFolderName = '', $sListPattern = '*', $bUseListStatus = false) {
        $sCmd = 'LSUB';
        if (!$bIsSubscribeList) {
            $sCmd = 'LIST';
        }

        $sListPattern = 0 === strlen(trim($sListPattern)) ? '*' : $sListPattern;

        $aParameters = [
            $this->EscapeString($sParentFolderName),
            $this->EscapeString($sListPattern)
        ];

        if ($bUseListStatus && $this->isSupported('LIST-STATUS')) {
            $aParameters[] = 'RETURN';
            $aParameters[] = [
                'STATUS',
                [
                    \CEmail_Client_Imap_Folder::FOLDER_STATUS_MESSAGES,
                    \CEmail_Client_Imap_Folder::FOLDER_STATUS_UNSEEN,
                    \CEmail_Client_Imap_Folder::FOLDER_STATUS_UIDNEXT
                ]
            ];
        } else {
            $bUseListStatus = false;
        }

        $this->sendRequest($sCmd, $aParameters);

        return $this->getFoldersFromResult(
            $this->parseResponseWithValidation(),
            $sCmd,
            $bUseListStatus
        );
    }

    /**
     * @param string $sParentFolderName = ''
     * @param string $sListPattern      = '*'
     *
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return array
     */
    public function folderList($sParentFolderName = '', $sListPattern = '*') {
        return $this->specificFolderList(false, $sParentFolderName, $sListPattern);
    }

    /**
     * @param string $sParentFolderName = ''
     * @param string $sListPattern      = '*'
     *
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return array
     */
    public function folderSubscribeList($sParentFolderName = '', $sListPattern = '*') {
        return $this->specificFolderList(true, $sParentFolderName, $sListPattern);
    }

    /**
     * @param string $sParentFolderName = ''
     * @param string $sListPattern      = '*'
     *
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return array
     */
    public function folderStatusList($sParentFolderName = '', $sListPattern = '*') {
        return $this->specificFolderList(false, $sParentFolderName, $sListPattern, true);
    }

    /**
     * @param array  $aResult
     * @param string $sFolderName
     * @param bool   $bIsWritable
     *
     * @return void
     */
    protected function initCurrentFolderInformation($aResult, $sFolderName, $bIsWritable) {
        if (\is_array($aResult)) {
            $oImapResponse = null;
            $oResult = CEmail_Client_Imap_FolderInformation::newInstance($sFolderName, $bIsWritable);

            foreach ($aResult as /* @var $oImapResponse \CEmail_Client\Imap\Response */ $oImapResponse) {
                if (\CEmail_Client_Imap_Response::RESPONSE_TYPE_UNTAGGED === $oImapResponse->responseType) {
                    if (\count($oImapResponse->responseList) > 2
                        && 'FLAGS' === $oImapResponse->responseList[1] && \is_array($oImapResponse->responseList[2])
                    ) {
                        $oResult->Flags = $oImapResponse->responseList[2];
                    }

                    if (is_array($oImapResponse->optionalResponse) && \count($oImapResponse->optionalResponse) > 1) {
                        if ('PERMANENTFLAGS' === $oImapResponse->optionalResponse[0]
                            && is_array($oImapResponse->optionalResponse[1])
                        ) {
                            $oResult->PermanentFlags = $oImapResponse->optionalResponse[1];
                        } elseif ('UIDVALIDITY' === $oImapResponse->optionalResponse[0]
                            && isset($oImapResponse->optionalResponse[1])
                        ) {
                            $oResult->Uidvalidity = $oImapResponse->optionalResponse[1];
                        } elseif ('UNSEEN' === $oImapResponse->optionalResponse[0]
                            && isset($oImapResponse->optionalResponse[1])
                            && is_numeric($oImapResponse->optionalResponse[1])
                        ) {
                            $oResult->Unread = (int) $oImapResponse->optionalResponse[1];
                        } elseif ('UIDNEXT' === $oImapResponse->optionalResponse[0]
                            && isset($oImapResponse->optionalResponse[1])
                        ) {
                            $oResult->Uidnext = $oImapResponse->optionalResponse[1];
                        }
                    }

                    if (\count($oImapResponse->responseList) > 2
                        && \is_string($oImapResponse->responseList[2])
                        && \is_numeric($oImapResponse->responseList[1])
                    ) {
                        switch($oImapResponse->responseList[2]) {
                            case 'EXISTS':
                                $oResult->Exists = (int) $oImapResponse->responseList[1];

                                break;
                            case 'RECENT':
                                $oResult->recent = (int) $oImapResponse->responseList[1];

                                break;
                        }
                    }
                }
            }

            $this->oCurrentFolderInfo = $oResult;
        }
    }

    /**
     * @param string $sFolderName
     * @param bool   $bIsWritable
     * @param bool   $bReSelectSameFolders
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    protected function selectOrExamineFolder($sFolderName, $bIsWritable, $bReSelectSameFolders) {
        if (!$bReSelectSameFolders) {
            if ($this->oCurrentFolderInfo
                && $sFolderName === $this->oCurrentFolderInfo->FolderName
                && $bIsWritable === $this->oCurrentFolderInfo->IsWritable
            ) {
                return $this;
            }
        }

        if (!\CBase_Validation::notEmptyString((string) $sFolderName, true)) {
            throw new \CEmail_Client_Exception_InvalidArgumentException();
        }

        $this->sendRequest(
            ($bIsWritable) ? 'SELECT' : 'EXAMINE',
            [$this->EscapeString($sFolderName)]
        );

        $this->initCurrentFolderInformation(
            $this->parseResponseWithValidation(),
            $sFolderName,
            $bIsWritable
        );

        $this->bIsSelected = true;

        return $this;
    }

    /**
     * @param string $sFolderName
     * @param bool   $bReSelectSameFolders = false
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function folderSelect($sFolderName, $bReSelectSameFolders = false) {
        return $this->selectOrExamineFolder($sFolderName, true, $bReSelectSameFolders);
    }

    /**
     * @param string $sFolderName
     * @param bool   $bReSelectSameFolders = false
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function folderExamine($sFolderName, $bReSelectSameFolders = false) {
        return $this->selectOrExamineFolder($sFolderName, $this->__FORCE_SELECT_ON_EXAMINE__, $bReSelectSameFolders);
    }

    /**
     * @param array  $aInputFetchItems
     * @param string $sIndexRange
     * @param bool   $bIndexIsUid
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return array
     */
    public function fetch(array $aInputFetchItems, $sIndexRange, $bIndexIsUid) {
        $sIndexRange = (string) $sIndexRange;
        if (!\CBase_Validation::notEmptyString($sIndexRange, true)) {
            $this->writeLogException(
                new \CEmail_Client_Exception_InvalidArgumentException(),
                \CLogger::ERROR,
                true
            );
        }

        $aFetchItems = \CEmail_Client_Imap_FetchType::changeFetchItemsBefourRequest($aInputFetchItems);
        foreach ($aFetchItems as $sName => $mItem) {
            if (0 < \strlen($sName) && '' !== $mItem) {
                if (null === $this->aFetchCallbacks) {
                    $this->aFetchCallbacks = [];
                }

                $this->aFetchCallbacks[$sName] = $mItem;
            }
        }

        $this->sendRequest((($bIndexIsUid) ? 'UID ' : '') . 'FETCH', [$sIndexRange, \array_keys($aFetchItems)]);
        $aResult = $this->validateResponse($this->parseResponse());
        $this->aFetchCallbacks = null;

        $aReturn = [];
        $oImapResponse = null;
        foreach ($aResult as $oImapResponse) {
            if (CEmail_Client_Imap_FetchResponse::isValidFetchImapResponse($oImapResponse)) {
                if (CEmail_Client_Imap_FetchResponse::isNotEmptyFetchImapResponse($oImapResponse)) {
                    $aReturn[] = CEmail_Client_Imap_FetchResponse::newInstance($oImapResponse);
                } else {
                    if ($this->logger) {
                        $this->logger->Write('Skipped Imap Response! [' . $oImapResponse->ToLine() . ']', \CLogger::NOTICE);
                    }
                }
            }
        }

        return $aReturn;
    }

    /**
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return array|false
     */
    public function quota() {
        $aReturn = false;
        if ($this->isSupported('QUOTA')) {
            $this->sendRequest('GETQUOTAROOT "INBOX"');
            $aResult = $this->parseResponseWithValidation();

            $aReturn = [0, 0];
            $oImapResponse = null;
            foreach ($aResult as /* @var $oImapResponse \CEmail_Client\Imap\Response */ $oImapResponse) {
                if (\CEmail_Client_Imap_Response::RESPONSE_TYPE_UNTAGGED === $oImapResponse->responseType
                    && 'QUOTA' === $oImapResponse->statusOrIndex
                    && \is_array($oImapResponse->responseList)
                    && isset($oImapResponse->responseList[3])
                    && \is_array($oImapResponse->responseList[3])
                    && 2 < \count($oImapResponse->responseList[3])
                    && 'STORAGE' === \strtoupper($oImapResponse->responseList[3][0])
                    && \is_numeric($oImapResponse->responseList[3][1])
                    && \is_numeric($oImapResponse->responseList[3][2])
                ) {
                    $aReturn = [
                        (int) $oImapResponse->responseList[3][1],
                        (int) $oImapResponse->responseList[3][2],
                        0,
                        0
                    ];

                    if (5 < \count($oImapResponse->responseList[3])
                        && 'MESSAGE' === \strtoupper($oImapResponse->responseList[3][3])
                        && \is_numeric($oImapResponse->responseList[3][4])
                        && \is_numeric($oImapResponse->responseList[3][5])
                    ) {
                        $aReturn[2] = (int) $oImapResponse->responseList[3][4];
                        $aReturn[3] = (int) $oImapResponse->responseList[3][5];
                    }

                    break;
                }
            }
        }

        return $aReturn;
    }

    /**
     * @param mixed $aValue
     *
     * @return mixed
     */
    private function validateThreadItem($aValue) {
        $mResult = false;
        if (\is_numeric($aValue)) {
            $mResult = (int) $aValue;
            if (0 >= $mResult) {
                $mResult = false;
            }
        } elseif (\is_array($aValue)) {
            if (1 === \count($aValue) && \is_numeric($aValue[0])) {
                $mResult = (int) $aValue[0];
                if (0 >= $mResult) {
                    $mResult = false;
                }
            } else {
                $mResult = [];
                foreach ($aValue as $aValueItem) {
                    $mTemp = $this->validateThreadItem($aValueItem);
                    if (false !== $mTemp) {
                        $mResult[] = $mTemp;
                    }
                }
            }
        }

        return $mResult;
    }

    /**
     * @return \CEmail_Client_Imap_FolderInformation
     */
    public function folderCurrentInformation() {
        return $this->oCurrentFolderInfo;
    }

    /**
     * @param string $sCommand
     * @param array  $aParams  = array()
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     *
     * @return void
     */
    public function sendRequest($sCommand, $aParams = []) {
        if (!\CBase_Validation::notEmptyString($sCommand, true) || !\is_array($aParams)) {
            $this->writeLogException(
                new \CEmail_Client_Exception_InvalidArgumentException(),
                \CLogger::ERROR,
                true
            );
        }

        $this->IsConnected(true);

        $sTag = $this->getNewTag();

        $sCommand = \trim($sCommand);
        $sRealCommand = $sTag . ' ' . $sCommand . $this->prepearParamLine($aParams);

        $sFakeCommand = '';
        $aFakeParams = $this->secureRequestParams($sCommand, $aParams);
        if (null !== $aFakeParams) {
            $sFakeCommand = $sTag . ' ' . $sCommand . $this->prepearParamLine($aFakeParams);
        }

        $this->aTagTimeouts[$sTag] = \microtime(true);
        $this->sendRaw($sRealCommand, true, $sFakeCommand);
    }

    /**
     * @param string $sCommand
     * @param array  $aParams
     *
     * @return null|array
     */
    private function secureRequestParams($sCommand, $aParams) {
        $aResult = null;
        switch ($sCommand) {
            case 'LOGIN':
                $aResult = $aParams;
                if (\is_array($aResult) && 2 === count($aResult)) {
                    $aResult[1] = '"********"';
                }

                break;
        }

        return $aResult;
    }

    /**
     * @param string $sCommand
     * @param array  $aParams   = array()
     * @param bool   $bFindCapa = false
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \CEmail_Client_Exception
     * @throws \CEmail_Client_Imap_Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function sendRequestWithCheck($sCommand, $aParams = [], $bFindCapa = false) {
        $this->sendRequest($sCommand, $aParams);
        $this->parseResponseWithValidation(null, $bFindCapa);

        return $this;
    }

    /**
     * @return array
     */
    public function getLastResponse() {
        return $this->aLastResponse;
    }

    /**
     * @param mixed $aResult
     *
     * @throws \CEmail_Client_Imap_Exception_ResponseNotFoundException
     * @throws \CEmail_Client_Imap_Exception_InvalidResponseException
     * @throws \CEmail_Client_Imap_Exception_NegativeResponseException
     *
     * @return array
     */
    private function validateResponse($aResult) {
        if (!\is_array($aResult) || 0 === $iCnt = \count($aResult)) {
            $this->writeLogException(
                new CEmail_Client_Imap_Exception_ResponseNotFoundException(),
                \CLogger::WARNING,
                true
            );
        }

        if ($aResult[$iCnt - 1]->responseType !== \CEmail_Client_Imap_Response::RESPONSE_TYPE_CONTINUATION) {
            if (!$aResult[$iCnt - 1]->isStatusResponse) {
                $this->writeLogException(
                    new CEmail_Client_Imap_Exception_InvalidResponseException($aResult),
                    \CLogger::WARNING,
                    true
                );
            }

            if (\CEmail_Client_Imap_Response::RESPONSE_STATUS_OK !== $aResult[$iCnt - 1]->statusOrIndex) {
                if (isset($aResult[$iCnt - 1]->responseList[2][0]) && strtoupper($aResult[$iCnt - 1]->responseList[2][0]) === 'ALREADYEXISTS') {
                    $this->writeLogException(
                        new \CEmail_Client_Mail_Exception_AlreadyExistsFolderException(),
                        \CLogger::WARNING,
                        true
                    );
                } else {
                    $this->writeLogException(
                        new CEmail_Client_Imap_Exception_NegativeResponseException($aResult),
                        \CLogger::WARNING,
                        true
                    );
                }
            }
        }

        return $aResult;
    }

    /**
     * @param string $sEndTag   = null
     * @param bool   $bFindCapa = false
     *
     * @return array|bool
     */
    protected function parseResponse($sEndTag = null, $bFindCapa = false) {
        if (\is_resource($this->connection)) {
            $oImapResponse = null;
            $sEndTag = (null === $sEndTag) ? $this->getCurrentTag() : $sEndTag;

            while (true) {
                $oImapResponse = CEmail_Client_Imap_Response::newInstance();

                $this->partialParseResponseBranch($oImapResponse);

                if ($oImapResponse) {
                    if (\CEmail_Client_Imap_Response::RESPONSE_TYPE_UNKNOWN === $oImapResponse->responseType) {
                        return false;
                    }

                    if ($bFindCapa) {
                        $this->initCapabilityImapResponse($oImapResponse);
                    }

                    $this->aPartialResponses[] = $oImapResponse;
                    if ($sEndTag === $oImapResponse->Tag || \CEmail_Client_Imap_Response::RESPONSE_TYPE_CONTINUATION === $oImapResponse->responseType) {
                        if (isset($this->aTagTimeouts[$sEndTag])) {
                            $this->writeLog(
                                (\microtime(true) - $this->aTagTimeouts[$sEndTag]) . ' (' . $sEndTag . ')',
                                \CLogger::INFO
                            );

                            unset($this->aTagTimeouts[$sEndTag]);
                        }

                        break;
                    }
                } else {
                    return false;
                }

                unset($oImapResponse);
            }
        }

        $this->iResponseBufParsedPos = 0;
        $this->aLastResponse = $this->aPartialResponses;
        $this->aPartialResponses = [];

        return $this->aLastResponse;
    }

    /**
     * @param string $sEndTag   = null
     * @param bool   $bFindCapa = false
     *
     * @return array
     */
    private function parseResponseWithValidation($sEndTag = null, $bFindCapa = false) {
        return $this->validateResponse($this->parseResponse($sEndTag, $bFindCapa));
    }

    /**
     * @param \CEmail_Client\Imap\Response $oImapResponse
     *
     * @return void
     */
    private function initCapabilityImapResponse($oImapResponse) {
        if (\CEmail_Client_Imap_Response::RESPONSE_TYPE_UNTAGGED === $oImapResponse->responseType
            && \is_array($oImapResponse->responseList)
        ) {
            $aList = null;
            if (isset($oImapResponse->responseList[1]) && \is_string($oImapResponse->responseList[1])
                && 'CAPABILITY' === \strtoupper($oImapResponse->responseList[1])
            ) {
                $aList = \array_slice($oImapResponse->responseList, 2);
            } elseif ($oImapResponse->optionalResponse && \is_array($oImapResponse->optionalResponse)
                && 1 < \count($oImapResponse->optionalResponse) && \is_string($oImapResponse->optionalResponse[0])
                && 'CAPABILITY' === \strtoupper($oImapResponse->optionalResponse[0])
            ) {
                $aList = \array_slice($oImapResponse->optionalResponse, 1);
            }

            if (\is_array($aList) && 0 < \count($aList)) {
                $this->aCapabilityItems = \array_map('strtoupper', $aList);
            }
        }
    }

    /**
     * @param mixed $oImapResponse
     * @param mixed $iStackIndex
     * @param mixed $bTreatAsAtom
     * @param mixed $sParentToken
     *
     * @throws \CEmail_Client_Exception
     *
     * @return array|string
     */
    private function partialParseResponseBranch(
        &$oImapResponse,
        $iStackIndex = -1,
        $bTreatAsAtom = false,
        $sParentToken = ''
    ) {
        $mNull = null;

        $iStackIndex++;
        $iPos = $this->iResponseBufParsedPos;

        $sPreviousAtomUpperCase = null;
        $bIsEndOfList = false;
        $bIsClosingBracketSquare = false;
        $iLiteralLen = 0;
        $iBufferEndIndex = 0;
        $iDebugCount = 0;

        $rImapLiteralStream = null;

        $bIsGotoDefault = false;
        $bIsGotoLiteral = false;
        $bIsGotoLiteralEnd = false;
        $bIsGotoAtomBracket = false;
        $bIsGotoNotAtomBracket = false;

        $bCountOneInited = false;
        $bCountTwoInited = false;

        $sAtomBuilder = $bTreatAsAtom ? '' : null;
        $aList = [];
        if (null !== $oImapResponse) {
            $aList = &$oImapResponse->responseList;
        }

        while (!$bIsEndOfList) {
            $iDebugCount++;
            if (100000 === $iDebugCount) {
                $this->Logger()->Write('PartialParseOver: ' . $iDebugCount, \CLogger::ERROR);
            }

            if ($this->bNeedNext) {
                $iPos = 0;
                $this->getNextBuffer();
                $this->iResponseBufParsedPos = $iPos;
                $this->bNeedNext = false;
            }

            $sChar = null;
            if ($bIsGotoDefault) {
                $sChar = 'GOTO_DEFAULT';
                $bIsGotoDefault = false;
            } elseif ($bIsGotoLiteral) {
                $bIsGotoLiteral = false;
                $bIsGotoLiteralEnd = true;

                if ($this->partialResponseLiteralCallbackCallable(
                    $sParentToken,
                    null === $sPreviousAtomUpperCase ? '' : \strtoupper($sPreviousAtomUpperCase),
                    $this->connection,
                    $iLiteralLen
                )
                ) {
                    if (!$bTreatAsAtom) {
                        $aList[] = '';
                    }
                } else {
                    $sLiteral = '';
                    $iRead = $iLiteralLen;

                    while (0 < $iRead) {
                        $sAddRead = \fread($this->connection, $iRead);
                        if (false === $sAddRead) {
                            $sLiteral = false;

                            break;
                        }

                        $sLiteral .= $sAddRead;
                        $iRead -= \strlen($sAddRead);

                        \CEmail_Client_Utils::resetTimeLimit();
                    }

                    if (false !== $sLiteral) {
                        $iLiteralSize = \strlen($sLiteral);
                        $this->loader->incStatistic('netRead', $iLiteralSize);
                        if ($iLiteralLen !== $iLiteralSize) {
                            $this->writeLog('Literal stream read warning "read ' . $iLiteralSize . ' of '
                                . $iLiteralLen . '" bytes', \CLogger::WARNING);
                        }

                        if (!$bTreatAsAtom) {
                            $aList[] = $sLiteral;

                            if ($this->getConfig('logSimpleLiterals')) {
                                $this->writeLog('{' . \strlen($sLiteral) . '} ' . $sLiteral, \CLogger::INFO);
                            }
                        }
                    } else {
                        $this->writeLog('Can\'t read imap stream', \CLogger::INFO);
                    }

                    unset($sLiteral);
                }

                continue;
            } elseif ($bIsGotoLiteralEnd) {
                $rImapLiteralStream = null;
                $sPreviousAtomUpperCase = null;
                $this->bNeedNext = true;
                $bIsGotoLiteralEnd = false;

                continue;
            } elseif ($bIsGotoAtomBracket) {
                if ($bTreatAsAtom) {
                    $sAtomBlock = $this->partialParseResponseBranch(
                        $mNull,
                        $iStackIndex,
                        true,
                        null === $sPreviousAtomUpperCase ? '' : \strtoupper($sPreviousAtomUpperCase)
                    );

                    $sAtomBuilder .= $sAtomBlock;
                    $iPos = $this->iResponseBufParsedPos;
                    $sAtomBuilder .= ($bIsClosingBracketSquare) ? ']' : ')';
                }

                $sPreviousAtomUpperCase = null;
                $bIsGotoAtomBracket = false;

                continue;
            } elseif ($bIsGotoNotAtomBracket) {
                $aSubItems = $this->partialParseResponseBranch(
                    $mNull,
                    $iStackIndex,
                    false,
                    null === $sPreviousAtomUpperCase ? '' : \strtoupper($sPreviousAtomUpperCase)
                );

                $aList[] = $aSubItems;
                $iPos = $this->iResponseBufParsedPos;
                $sPreviousAtomUpperCase = null;
                if (null !== $oImapResponse && $oImapResponse->isStatusResponse) {
                    $oImapResponse->optionalResponse = $aSubItems;

                    $bIsGotoDefault = true;
                    $bIsGotoNotAtomBracket = false;

                    continue;
                }
                $bIsGotoNotAtomBracket = false;

                continue;
            } else {
                $iBufferEndIndex = \strlen($this->sResponseBuffer) - 3;
                $this->bResponseBufferChanged = false;

                if ($iPos > $iBufferEndIndex) {
                    break;
                }

                $sChar = $this->sResponseBuffer[$iPos];
            }

            switch ($sChar) {
                case ']':
                case ')':
                    $iPos++;
                    $sPreviousAtomUpperCase = null;
                    $bIsEndOfList = true;

                    break;
                case ' ':
                    if ($bTreatAsAtom) {
                        $sAtomBuilder .= ' ';
                    }
                    $iPos++;

                    break;
                case '[':
                    $bIsClosingBracketSquare = true;
                    // no break
                case '(':
                    if ($bTreatAsAtom) {
                        $sAtomBuilder .= ($bIsClosingBracketSquare) ? '[' : '(';
                    }
                    $iPos++;

                    $this->iResponseBufParsedPos = $iPos;
                    if ($bTreatAsAtom) {
                        $bIsGotoAtomBracket = true;
                    } else {
                        $bIsGotoNotAtomBracket = true;
                    }

                    break;
                case '{':
                    $bIsLiteralParsed = false;
                    $mLiteralEndPos = \strpos($this->sResponseBuffer, '}', $iPos);
                    if (false !== $mLiteralEndPos && $mLiteralEndPos > $iPos) {
                        $sLiteralLenAsString = \substr($this->sResponseBuffer, $iPos + 1, $mLiteralEndPos - $iPos - 1);
                        if (\is_numeric($sLiteralLenAsString)) {
                            $iLiteralLen = (int) $sLiteralLenAsString;
                            $bIsLiteralParsed = true;
                            $iPos = $mLiteralEndPos + 3;
                            $bIsGotoLiteral = true;

                            break;
                        }
                    }
                    if (!$bIsLiteralParsed) {
                        $iPos = $iBufferEndIndex;
                    }
                    $sPreviousAtomUpperCase = null;

                    break;
                case '"':
                    $bIsQuotedParsed = false;
                    while (true) {
                        $iClosingPos = $iPos + 1;
                        if ($iClosingPos > $iBufferEndIndex) {
                            break;
                        }

                        while (true) {
                            $iClosingPos = \strpos($this->sResponseBuffer, '"', $iClosingPos);
                            if (false === $iClosingPos) {
                                break;
                            }

                            $iClosingPosNext = $iClosingPos + 1;
                            if (isset($this->sResponseBuffer[$iClosingPosNext])
                                && ' ' !== $this->sResponseBuffer[$iClosingPosNext]
                                && "\r" !== $this->sResponseBuffer[$iClosingPosNext]
                                && "\n" !== $this->sResponseBuffer[$iClosingPosNext]
                                && ']' !== $this->sResponseBuffer[$iClosingPosNext]
                                && ')' !== $this->sResponseBuffer[$iClosingPosNext]
                            ) {
                                $iClosingPos++;

                                continue;
                            }

                            $iSlashCount = 0;
                            while ('\\' === $this->sResponseBuffer[$iClosingPos - $iSlashCount - 1]) {
                                $iSlashCount++;
                            }

                            if ($iSlashCount % 2 == 1) {
                                $iClosingPos++;

                                continue;
                            } else {
                                break;
                            }
                        }

                        if (false === $iClosingPos) {
                            break;
                        } else {
                            // $iSkipClosingPos = 0;
                            $bIsQuotedParsed = true;
                            if ($bTreatAsAtom) {
                                $sAtomBuilder .= \strtr(
                                    \substr($this->sResponseBuffer, $iPos, $iClosingPos - $iPos + 1),
                                    ['\\\\' => '\\', '\\"' => '"']
                                );
                            } else {
                                $aList[] = \strtr(
                                    \substr($this->sResponseBuffer, $iPos + 1, $iClosingPos - $iPos - 1),
                                    ['\\\\' => '\\', '\\"' => '"']
                                );
                            }

                            $iPos = $iClosingPos + 1;

                            break;
                        }
                    }

                    if (!$bIsQuotedParsed) {
                        $iPos = $iBufferEndIndex;
                    }

                    $sPreviousAtomUpperCase = null;

                    break;

                case 'GOTO_DEFAULT':
                default:
                    $iCharBlockStartPos = $iPos;

                    if (null !== $oImapResponse && $oImapResponse->isStatusResponse) {
                        $iPos = $iBufferEndIndex;

                        while ($iPos > $iCharBlockStartPos && $this->sResponseBuffer[$iCharBlockStartPos] == ' ') {
                            $iCharBlockStartPos++;
                        }
                    }

                    $bIsAtomDone = false;
                    while (!$bIsAtomDone && ($iPos <= $iBufferEndIndex)) {
                        $sCharDef = $this->sResponseBuffer[$iPos];
                        switch ($sCharDef) {
                            case '[':
                                if (null === $sAtomBuilder) {
                                    $sAtomBuilder = '';
                                }

                                $sAtomBuilder .= \substr($this->sResponseBuffer, $iCharBlockStartPos, $iPos - $iCharBlockStartPos + 1);

                                $iPos++;
                                $this->iResponseBufParsedPos = $iPos;

                                $sListBlock = $this->partialParseResponseBranch(
                                    $mNull,
                                    $iStackIndex,
                                    true,
                                    null === $sPreviousAtomUpperCase ? '' : \strtoupper($sPreviousAtomUpperCase)
                                );

                                if (null !== $sListBlock) {
                                    $sAtomBuilder .= $sListBlock . ']';
                                }

                                $iPos = $this->iResponseBufParsedPos;
                                $iCharBlockStartPos = $iPos;

                                break;
                            case ' ':
                            case ']':
                            case ')':
                                $bIsAtomDone = true;

                                break;
                            default:
                                $iPos++;

                                break;
                        }
                    }

                    if ($iPos > $iCharBlockStartPos || null !== $sAtomBuilder) {
                        $sLastCharBlock = \substr($this->sResponseBuffer, $iCharBlockStartPos, $iPos - $iCharBlockStartPos);
                        if (null === $sAtomBuilder) {
                            $aList[] = $sLastCharBlock;
                            $sPreviousAtomUpperCase = $sLastCharBlock;
                        } else {
                            $sAtomBuilder .= $sLastCharBlock;

                            if (!$bTreatAsAtom) {
                                $aList[] = $sAtomBuilder;
                                $sPreviousAtomUpperCase = $sAtomBuilder;
                                $sAtomBuilder = null;
                            }
                        }

                        if (null !== $oImapResponse) {
                            // if (1 === \count($aList))
                            if (!$bCountOneInited && 1 === \count($aList)) {
                                // if (isset($aList[0]) && !isset($aList[1])) // fast 1 === \count($aList)
                                $bCountOneInited = true;

                                $oImapResponse->Tag = $aList[0];
                                if ('+' === $oImapResponse->Tag) {
                                    $oImapResponse->responseType = \CEmail_Client_Imap_Response::RESPONSE_TYPE_CONTINUATION;
                                } elseif ('*' === $oImapResponse->Tag) {
                                    $oImapResponse->responseType = \CEmail_Client_Imap_Response::RESPONSE_TYPE_UNTAGGED;
                                } elseif ($this->getCurrentTag() === $oImapResponse->Tag) {
                                    $oImapResponse->responseType = \CEmail_Client_Imap_Response::RESPONSE_TYPE_TAGGED;
                                } else {
                                    $oImapResponse->responseType = \CEmail_Client_Imap_Response::RESPONSE_TYPE_UNKNOWN;
                                }
                            } elseif (!$bCountTwoInited && 2 === \count($aList)) {
                                $bCountTwoInited = true;

                                $oImapResponse->statusOrIndex = strtoupper($aList[1]);

                                if ($oImapResponse->statusOrIndex == \CEmail_Client_Imap_Response::RESPONSE_STATUS_OK
                                    || $oImapResponse->statusOrIndex == \CEmail_Client_Imap_Response::RESPONSE_STATUS_NO
                                    || $oImapResponse->statusOrIndex == \CEmail_Client_Imap_Response::RESPONSE_STATUS_BAD
                                    || $oImapResponse->statusOrIndex == \CEmail_Client_Imap_Response::RESPONSE_STATUS_BYE
                                    || $oImapResponse->statusOrIndex == \CEmail_Client_Imap_Response::RESPONSE_STATUS_PREAUTH
                                ) {
                                    $oImapResponse->isStatusResponse = true;
                                }
                            } elseif (\CEmail_Client_Imap_Response::RESPONSE_TYPE_CONTINUATION === $oImapResponse->responseType) {
                                $oImapResponse->HumanReadable = $sLastCharBlock;
                            } elseif ($oImapResponse->isStatusResponse) {
                                $oImapResponse->HumanReadable = $sLastCharBlock;
                            }
                        }
                    }
            }
        }

        $this->iResponseBufParsedPos = $iPos;
        if (null !== $oImapResponse) {
            $this->bNeedNext = true;
            $this->iResponseBufParsedPos = 0;
        }

        if (100000 < $iDebugCount) {
            $this->Logger()->Write('PartialParseOverResult: ' . $iDebugCount, \CLogger::ERROR);
        }

        return $bTreatAsAtom ? $sAtomBuilder : $aList;
    }

    /**
     * @param string   $sParent
     * @param string   $sLiteralAtomUpperCase
     * @param resource $rImapStream
     * @param int      $iLiteralLen
     *
     * @return bool
     */
    private function partialResponseLiteralCallbackCallable($sParent, $sLiteralAtomUpperCase, $rImapStream, $iLiteralLen) {
        $sLiteralAtomUpperCasePeek = '';
        if (0 === \strpos($sLiteralAtomUpperCase, 'BODY')) {
            $sLiteralAtomUpperCasePeek = \str_replace('BODY', 'BODY.PEEK', $sLiteralAtomUpperCase);
        }

        $sFetchKey = '';
        if (\is_array($this->aFetchCallbacks)) {
            if (0 < \strlen($sLiteralAtomUpperCasePeek) && isset($this->aFetchCallbacks[$sLiteralAtomUpperCasePeek])) {
                $sFetchKey = $sLiteralAtomUpperCasePeek;
            } elseif (0 < \strlen($sLiteralAtomUpperCase) && isset($this->aFetchCallbacks[$sLiteralAtomUpperCase])) {
                $sFetchKey = $sLiteralAtomUpperCase;
            }
        }

        $bResult = false;
        if (0 < \strlen($sFetchKey) && '' !== $this->aFetchCallbacks[$sFetchKey]
            && \is_callable($this->aFetchCallbacks[$sFetchKey])
        ) {
            $rImapLiteralStream
                = \CEmail_Client_StreamWrapper_Literal::createStream($rImapStream, $iLiteralLen, $this->loader);

            $bResult = true;
            $this->writeLog('Start Callback for ' . $sParent . ' / ' . $sLiteralAtomUpperCase
                . ' - try to read ' . $iLiteralLen . ' bytes.', \CLogger::INFO);

            $this->isRunningCallback = true;

            try {
                \call_user_func(
                    $this->aFetchCallbacks[$sFetchKey],
                    $sParent,
                    $sLiteralAtomUpperCase,
                    $rImapLiteralStream
                );
            } catch (\Exception $oException) {
                $this->writeLog('Callback Exception', \CLogger::NOTICE);
                $this->writeLogException($oException);
            }

            if (\is_resource($rImapLiteralStream)) {
                $iNotReadLiteralLen = 0;

                $bFeof = \feof($rImapLiteralStream);
                $this->writeLog('End Callback for ' . $sParent . ' / ' . $sLiteralAtomUpperCase
                    . ' - feof = ' . ($bFeof ? 'good' : 'BAD'), $bFeof
                        ? \CLogger::INFO : \CLogger::WARNING);

                if (!$bFeof) {
                    while (!@\feof($rImapLiteralStream)) {
                        $sBuf = @\fread($rImapLiteralStream, 1024 * 1024);
                        if (false === $sBuf || 0 === \strlen($sBuf) || null === $sBuf) {
                            break;
                        }

                        \CEmail_Client_Utils::resetTimeLimit();
                        $iNotReadLiteralLen += \strlen($sBuf);
                    }

                    if (\is_resource($rImapLiteralStream) && !@\feof($rImapLiteralStream)) {
                        @\stream_get_contents($rImapLiteralStream);
                    }
                }

                if (\is_resource($rImapLiteralStream)) {
                    @\fclose($rImapLiteralStream);
                }

                if ($iNotReadLiteralLen > 0) {
                    $this->writeLog(
                        'Not read literal size is ' . $iNotReadLiteralLen . ' bytes.',
                        \CLogger::WARNING
                    );
                }
            } else {
                $this->writeLog(
                    'Literal stream is not resource after callback.',
                    \CLogger::WARNING
                );
            }

            $this->loader->incStatistic('netRead', $iLiteralLen);

            $this->isRunningCallback = false;
        }

        return $bResult;
    }

    /**
     * @param array $aParams = null
     *
     * @return string
     */
    private function prepearParamLine($aParams = []) {
        $sReturn = '';
        if (\is_array($aParams) && 0 < \count($aParams)) {
            foreach ($aParams as $mParamItem) {
                if (\is_array($mParamItem) && 0 < \count($mParamItem)) {
                    $sReturn .= ' (' . \trim($this->prepearParamLine($mParamItem)) . ')';
                } elseif (\is_string($mParamItem)) {
                    $sReturn .= ' ' . $mParamItem;
                }
            }
        }

        return $sReturn;
    }

    /**
     * @return string
     */
    private function getNewTag() {
        $this->iTagCount++;

        return $this->getCurrentTag();
    }

    /**
     * @return string
     */
    private function getCurrentTag() {
        return self::TAG_PREFIX . $this->iTagCount;
    }

    /**
     * @param string $sStringForEscape
     *
     * @return string
     */
    public function escapeString($sStringForEscape) {
        return '"' . \str_replace(['\\', '"'], ['\\\\', '\\"'], $sStringForEscape) . '"';
    }

    /**
     * @return string
     */
    protected function getLogName() {
        return 'IMAP';
    }

    /**
     * @param \CEmail_Client\Log\Logger $logger
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     *
     * @return \CEmail_Client_ImapClient
     */
    public function setLogger($logger) {
        parent::setLogger($logger);

        return $this;
    }

    /**
     * @param resource $connection
     * @param array    $aCapabilityItems = array()
     *
     * @return \CEmail_Client_ImapClient
     */
    public function testSetValues($connection, $aCapabilityItems = []) {
        $this->connection = $connection;
        $this->aCapabilityItems = $aCapabilityItems;

        return $this;
    }

    /**
     * @param string $sEndTag   = null
     * @param string $bFindCapa = false
     *
     * @return array
     */
    public function testParseResponseWithValidationProxy($sEndTag = null, $bFindCapa = false) {
        return $this->parseResponseWithValidation($sEndTag, $bFindCapa);
    }
}
