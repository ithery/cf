<?php
trait CEmail_Client_Imap_Trait_ImapClientMessageTrait {
    /**
     * @param string $sSearchCriterias = 'ALL'
     * @param bool   $bReturnUid       = true
     * @param string $sCharset         = \MailSo\Base\Enumerations\Charset::UTF_8
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     *
     * @return array
     */
    public function messageSimpleThread($sSearchCriterias = 'ALL', $bReturnUid = true, $sCharset = \MailSo\Base\Enumerations\Charset::UTF_8) {
        $sCommandPrefix = ($bReturnUid) ? 'UID ' : '';
        $sSearchCriterias = !\CBase_Validation::notEmptyString($sSearchCriterias, true) || '*' === $sSearchCriterias
            ? 'ALL' : $sSearchCriterias;

        $sThreadType = '';
        switch (true) {
            case $this->isSupported('THREAD=REFS'):
                $sThreadType = 'REFS';

                break;
            case $this->isSupported('THREAD=REFERENCES'):
                $sThreadType = 'REFERENCES';

                break;
            case $this->isSupported('THREAD=ORDEREDSUBJECT'):
                $sThreadType = 'ORDEREDSUBJECT';

                break;
            default:
                $this->writeLogException(
                    new CEmail_Client_Imap_Exception_RuntimeException('Thread is not supported'),
                    \CLogger::ERROR,
                    true
                );

                break;
        }

        $aRequest = [];
        $aRequest[] = $sThreadType;
        $aRequest[] = \strtoupper($sCharset);
        $aRequest[] = $sSearchCriterias;

        $sCmd = 'THREAD';

        $this->sendRequest($sCommandPrefix . $sCmd, $aRequest);
        $aResult = $this->parseResponseWithValidation();

        $aReturn = [];
        $oImapResponse = null;

        foreach ($aResult as /* @var $oImapResponse \MailSo\Imap\Response */ $oImapResponse) {
            if (\CEmail_Client_Imap_Response::RESPONSE_TYPE_UNTAGGED === $oImapResponse->responseType
                && ($sCmd === $oImapResponse->statusOrIndex || ($bReturnUid && 'UID' === $oImapResponse->statusOrIndex) && !empty($oImapResponse->responseList[2]) && $sCmd === $oImapResponse->responseList[2])
                && \is_array($oImapResponse->responseList)
                && 2 < \count($oImapResponse->responseList)
            ) {
                $iStart = 2;
                if ($bReturnUid && 'UID' === $oImapResponse->statusOrIndex
                    && !empty($oImapResponse->responseList[2])
                    && $sCmd === $oImapResponse->responseList[2]
                ) {
                    $iStart = 3;
                }

                for ($iIndex = $iStart, $iLen = \count($oImapResponse->responseList); $iIndex < $iLen; $iIndex++) {
                    $aNewValue = $this->validateThreadItem($oImapResponse->responseList[$iIndex]);
                    if (false !== $aNewValue) {
                        $aReturn[] = $aNewValue;
                    }
                }
            }
        }

        return $aReturn;
    }

    /**
     * @param string $sToFolder
     * @param string $sIndexRange
     * @param bool   $bIndexIsUid
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function messageCopy($sToFolder, $sIndexRange, $bIndexIsUid) {
        if (0 === \strlen($sIndexRange)) {
            $this->writeLogException(
                new \CEmail_Client_Exception_InvalidArgumentException(),
                \CLogger::ERROR,
                true
            );
        }

        $sCommandPrefix = ($bIndexIsUid) ? 'UID ' : '';

        return $this->sendRequestWithCheck(
            $sCommandPrefix . 'COPY',
            [$sIndexRange, $this->EscapeString($sToFolder)]
        );
    }

    /**
     * @param string $sToFolder
     * @param string $sIndexRange
     * @param bool   $bIndexIsUid
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function messageMove($sToFolder, $sIndexRange, $bIndexIsUid) {
        if (0 === \strlen($sIndexRange)) {
            $this->writeLogException(
                new \CEmail_Client_Exception_InvalidArgumentException(),
                \CLogger::ERROR,
                true
            );
        }

        if (!$this->isSupported('MOVE')) {
            $this->writeLogException(
                new CEmail_Client_Imap_Exception_RuntimeException('Move is not supported'),
                \CLogger::ERROR,
                true
            );
        }

        $sCommandPrefix = ($bIndexIsUid) ? 'UID ' : '';

        return $this->sendRequestWithCheck(
            $sCommandPrefix . 'MOVE',
            [$sIndexRange, $this->EscapeString($sToFolder)]
        );
    }

    /**
     * @param string $sUidRangeIfSupported = ''
     * @param bool   $bForceUidExpunge     = false
     * @param bool   $bExpungeAll          = false
     *
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function messageExpunge($sUidRangeIfSupported = '', $bForceUidExpunge = false, $bExpungeAll = false) {
        $sUidRangeIfSupported = \trim($sUidRangeIfSupported);

        $sCmd = 'EXPUNGE';
        $aArguments = [];

        if (!$bExpungeAll && $bForceUidExpunge && 0 < \strlen($sUidRangeIfSupported) && $this->isSupported('UIDPLUS')) {
            $sCmd = 'UID ' . $sCmd;
            $aArguments = [$sUidRangeIfSupported];
        }

        return $this->sendRequestWithCheck($sCmd, $aArguments);
    }

    /**
     * @param string $sIndexRange
     * @param bool   $bIndexIsUid
     * @param array  $aInputStoreItems
     * @param string $sStoreAction
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function messageStoreFlag($sIndexRange, $bIndexIsUid, $aInputStoreItems, $sStoreAction) {
        if (!\CBase_Validation::notEmptyString($sIndexRange, true)
            || !\CBase_Validation::notEmptyString($sStoreAction, true)
            || 0 === \count($aInputStoreItems)
        ) {
            return false;
        }

        $sCmd = ($bIndexIsUid) ? 'UID STORE' : 'STORE';

        return $this->sendRequestWithCheck($sCmd, [$sIndexRange, $sStoreAction, $aInputStoreItems]);
    }

    /**
     * @param string $sMessageFileName
     * @param string $sFolderToSave
     * @param array  $aAppendFlags     = null
     * @param int    &$iUid            = null
     *
     * @return \MailSo\Mail\MailClient
     */
    public function messageAppendFile($sMessageFileName, $sFolderToSave, $aAppendFlags = null, &$iUid = null) {
        if (!@\is_file($sMessageFileName) || !@\is_readable($sMessageFileName)) {
            throw new \CEmail_Client_Exception_InvalidArgumentException();
        }

        $iMessageStreamSize = \filesize($sMessageFileName);
        $rMessageStream = \fopen($sMessageFileName, 'rb');

        $this->MessageAppendStream($sFolderToSave, $rMessageStream, $iMessageStreamSize, $aAppendFlags, $iUid);

        if (\is_resource($rMessageStream)) {
            @fclose($rMessageStream);
        }

        return $this;
    }

    /**
     * @param string   $sFolderName
     * @param resource $rMessageAppendStream
     * @param int      $iStreamSize
     * @param array    $aAppendFlags         = null
     * @param int      $iUid                 = null
     * @param int      $sDateTime            = 0
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     *
     * @return \CEmail_Client_ImapClient
     */
    public function messageAppendStream($sFolderName, $rMessageAppendStream, $iStreamSize, $aAppendFlags = null, &$iUid = null, $sDateTime = 0) {
        $aData = [$this->EscapeString($sFolderName), $aAppendFlags];
        if (0 < $sDateTime) {
            $aData[] = $this->EscapeString(\gmdate('d-M-Y H:i:s', $sDateTime) . ' +0000');
        }

        $aData[] = '{' . $iStreamSize . '}';

        $this->sendRequest('APPEND', $aData);
        $this->parseResponseWithValidation();

        $this->writeLog('Write to connection stream', \CLogger::INFO);

        \MailSo\Base\Utils::MultipleStreamWriter($rMessageAppendStream, [$this->rConnect]);

        $this->sendRaw('');
        $this->parseResponseWithValidation();

        if (null !== $iUid) {
            $aLastResponse = $this->GetLastResponse();
            if (\is_array($aLastResponse) && 0 < \count($aLastResponse) && $aLastResponse[\count($aLastResponse) - 1]) {
                $oLast = $aLastResponse[count($aLastResponse) - 1];
                if ($oLast && \CEmail_Client_Imap_Response::RESPONSE_TYPE_TAGGED === $oLast->responseType && \is_array($oLast->OptionalResponse)) {
                    if (0 < \strlen($oLast->OptionalResponse[0])
                        && 0 < \strlen($oLast->OptionalResponse[2])
                        && 'APPENDUID' === strtoupper($oLast->OptionalResponse[0])
                        && \is_numeric($oLast->OptionalResponse[2])
                    ) {
                        $iUid = (int) $oLast->OptionalResponse[2];
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param array  $aSortTypes
     * @param string $sSearchCriterias
     * @param bool   $bReturnUid
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     *
     * @return array
     */
    public function messageSimpleSort($aSortTypes, $sSearchCriterias = 'ALL', $bReturnUid = true) {
        $sCommandPrefix = ($bReturnUid) ? 'UID ' : '';
        $sSearchCriterias = !\CBase_Validation::notEmptyString($sSearchCriterias, true) || '*' === $sSearchCriterias
            ? 'ALL' : $sSearchCriterias;

        if (!\is_array($aSortTypes) || 0 === \count($aSortTypes)) {
            $this->writeLogException(
                new \CEmail_Client_Exception_InvalidArgumentException(),
                \CLogger::ERROR,
                true
            );
        } elseif (!$this->isSupported('SORT')) {
            $this->writeLogException(
                new \CEmail_Client_Exception_InvalidArgumentException(),
                \CLogger::ERROR,
                true
            );
        }

        $aRequest = [];
        $aRequest[] = $aSortTypes;
        $aRequest[] = \cstr::isAscii($sSearchCriterias) ? 'US-ASCII' : 'UTF-8';
        $aRequest[] = $sSearchCriterias;

        $sCmd = 'SORT';

        $this->sendRequest($sCommandPrefix . $sCmd, $aRequest);
        $aResult = $this->parseResponseWithValidation();

        $aReturn = [];
        $oImapResponse = null;
        foreach ($aResult as /* @var $oImapResponse \MailSo\Imap\Response */ $oImapResponse) {
            if (\CEmail_Client_Imap_Response::RESPONSE_TYPE_UNTAGGED === $oImapResponse->responseType
                && ($sCmd === $oImapResponse->statusOrIndex || ($bReturnUid && 'UID' === $oImapResponse->statusOrIndex) && !empty($oImapResponse->responseList[2]) && $sCmd === $oImapResponse->responseList[2])
                && \is_array($oImapResponse->responseList)
                && 2 < \count($oImapResponse->responseList)
            ) {
                $iStart = 2;
                if ($bReturnUid && 'UID' === $oImapResponse->statusOrIndex
                    && !empty($oImapResponse->responseList[2])
                    && $sCmd === $oImapResponse->responseList[2]
                ) {
                    $iStart = 3;
                }

                for ($iIndex = $iStart, $iLen = \count($oImapResponse->responseList); $iIndex < $iLen; $iIndex++) {
                    $aReturn[] = (int) $oImapResponse->responseList[$iIndex];
                }
            }
        }

        return $aReturn;
    }

    /**
     * @param bool   $bSort               = false
     * @param string $sSearchCriterias    = 'ALL'
     * @param array  $aSearchOrSortReturn = null
     * @param bool   $bReturnUid          = true
     * @param string $sLimit              = ''
     * @param string $sCharset            = ''
     * @param array  $aSortTypes          = null
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     *
     * @return array
     */
    private function simpleESearchOrESortHelper($bSort = false, $sSearchCriterias = 'ALL', $aSearchOrSortReturn = null, $bReturnUid = true, $sLimit = '', $sCharset = '', $aSortTypes = null) {
        $sCommandPrefix = ($bReturnUid) ? 'UID ' : '';
        $sSearchCriterias = 0 === \strlen($sSearchCriterias) || '*' === $sSearchCriterias
            ? 'ALL' : $sSearchCriterias;

        $sCmd = $bSort ? 'SORT' : 'SEARCH';
        if ($bSort && (!\is_array($aSortTypes) || 0 === \count($aSortTypes) || !$this->isSupported('SORT'))) {
            $this->writeLogException(
                new \CEmail_Client_Exception_InvalidArgumentException(),
                \CLogger::ERROR,
                true
            );
        }

        if (!$this->isSupported($bSort ? 'ESORT' : 'ESEARCH')) {
            $this->writeLogException(
                new \CEmail_Client_Exception_InvalidArgumentException(),
                \CLogger::ERROR,
                true
            );
        }

        if (!\is_array($aSearchOrSortReturn) || 0 === \count($aSearchOrSortReturn)) {
            $aSearchOrSortReturn = ['ALL'];
        }

        $aRequest = [];
        if ($bSort) {
            $aRequest[] = 'RETURN';
            $aRequest[] = $aSearchOrSortReturn;

            $aRequest[] = $aSortTypes;
            $aRequest[] = \cstr::IsAscii($sSearchCriterias) ? 'US-ASCII' : 'UTF-8';
        } else {
            if (0 < \strlen($sCharset)) {
                $aRequest[] = 'CHARSET';
                $aRequest[] = \strtoupper($sCharset);
            }

            $aRequest[] = 'RETURN';
            $aRequest[] = $aSearchOrSortReturn;
        }

        $aRequest[] = $sSearchCriterias;

        if (0 < \strlen($sLimit)) {
            $aRequest[] = $sLimit;
        }

        $this->sendRequest($sCommandPrefix . $sCmd, $aRequest);
        $sRequestTag = $this->getCurrentTag();

        $aResult = [];
        $aResponse = $this->parseResponseWithValidation();

        if (\is_array($aResponse)) {
            $oImapResponse = null;
            foreach ($aResponse as /* @var $oImapResponse \MailSo\Imap\Response */ $oImapResponse) {
                if (\CEmail_Client_Imap_Response::RESPONSE_TYPE_UNTAGGED === $oImapResponse->responseType
                    && 'ESEARCH' === $oImapResponse->statusOrIndex
                    && \is_array($oImapResponse->responseList)
                    && isset($oImapResponse->responseList[2], $oImapResponse->responseList[2][0], $oImapResponse->responseList[2][1])
                    && 'TAG' === $oImapResponse->responseList[2][0] && $sRequestTag === $oImapResponse->responseList[2][1]
                    && (!$bReturnUid || ($bReturnUid && !empty($oImapResponse->responseList[3]) && 'UID' === $oImapResponse->responseList[3]))
                ) {
                    $iStart = 3;
                    foreach ($oImapResponse->responseList as $iIndex => $mItem) {
                        if ($iIndex >= $iStart) {
                            switch ($mItem) {
                                case 'ALL':
                                case 'MAX':
                                case 'MIN':
                                case 'COUNT':
                                    if (isset($oImapResponse->responseList[$iIndex + 1])) {
                                        $aResult[$mItem] = $oImapResponse->responseList[$iIndex + 1];
                                    }

                                    break;
                            }
                        }
                    }
                }
            }
        }

        return $aResult;
    }

    /**
     * @param string $sSearchCriterias = 'ALL'
     * @param array  $aSearchReturn    = null
     * @param bool   $bReturnUid       = true
     * @param string $sLimit           = ''
     * @param string $sCharset         = ''
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     *
     * @return array
     */
    public function messageSimpleESearch($sSearchCriterias = 'ALL', $aSearchReturn = null, $bReturnUid = true, $sLimit = '', $sCharset = '') {
        return $this->simpleESearchOrESortHelper(false, $sSearchCriterias, $aSearchReturn, $bReturnUid, $sLimit, $sCharset);
    }

    /**
     * @param array  $aSortTypes
     * @param string $sSearchCriterias = 'ALL'
     * @param array  $aSearchReturn    = null
     * @param bool   $bReturnUid       = true
     * @param string $sLimit           = ''
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     *
     * @return array
     */
    public function messageSimpleESort($aSortTypes, $sSearchCriterias = 'ALL', $aSearchReturn = null, $bReturnUid = true, $sLimit = '') {
        return $this->simpleESearchOrESortHelper(true, $sSearchCriterias, $aSearchReturn, $bReturnUid, $sLimit, '', $aSortTypes);
    }

    /**
     * @param string $sSearchCriterias
     * @param bool   $bReturnUid       = true
     * @param string $sCharset         = ''
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     *
     * @return array
     */
    public function messageSimpleSearch($sSearchCriterias = 'ALL', $bReturnUid = true, $sCharset = '') {
        $sCommandPrefix = ($bReturnUid) ? 'UID ' : '';
        $sSearchCriterias = 0 === \strlen($sSearchCriterias) || '*' === $sSearchCriterias
            ? 'ALL' : $sSearchCriterias;

        $aRequest = [];
        if (0 < \strlen($sCharset)) {
            $aRequest[] = 'CHARSET';
            $aRequest[] = \strtoupper($sCharset);
        }

        $aRequest[] = $sSearchCriterias;

        $sCmd = 'SEARCH';

        $this->sendRequest($sCommandPrefix . $sCmd, $aRequest);
        $aResult = $this->parseResponseWithValidation();

        $aReturn = [];
        $oImapResponse = null;
        foreach ($aResult as /* @var $oImapResponse \MailSo\Imap\Response */ $oImapResponse) {
            if (\CEmail_Client_Imap_Response::RESPONSE_TYPE_UNTAGGED === $oImapResponse->responseType
                && ($sCmd === $oImapResponse->statusOrIndex || ($bReturnUid && 'UID' === $oImapResponse->statusOrIndex) && !empty($oImapResponse->responseList[2]) && $sCmd === $oImapResponse->responseList[2])
                && \is_array($oImapResponse->responseList)
                && 2 < count($oImapResponse->responseList)
            ) {
                $iStart = 2;
                if ($bReturnUid && 'UID' === $oImapResponse->statusOrIndex
                    && !empty($oImapResponse->responseList[2])
                    && $sCmd === $oImapResponse->responseList[2]
                ) {
                    $iStart = 3;
                }

                for ($iIndex = $iStart, $iLen = \count($oImapResponse->responseList); $iIndex < $iLen; $iIndex++) {
                    $aReturn[] = (int) $oImapResponse->responseList[$iIndex];
                }
            }
        }

        $aReturn = \array_reverse($aReturn);

        return $aReturn;
    }
}
