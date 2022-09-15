<?php

class CEmail_Client_Imap_FetchResponse {
    /**
     * @var \CEmail_Client_Imap_Response
     */
    private $oImapResponse;

    /**
     * @var null|array
     */
    private $aEnvelopeCache;

    /**
     * @param \CEmail_Client_Imap_Response $oImapResponse
     */
    private function __construct($oImapResponse) {
        $this->oImapResponse = $oImapResponse;
        $this->aEnvelopeCache = null;
    }

    /**
     * @param \CEmail_Client_Imap_Response $oImapResponse
     *
     * @return \CEmail_Client_Imap_FetchResponse
     */
    public static function newInstance($oImapResponse) {
        return new self($oImapResponse);
    }

    /**
     * @param bool $bForce = false
     *
     * @return null|array
     */
    public function getEnvelope($bForce = false) {
        if (null === $this->aEnvelopeCache || $bForce) {
            $this->aEnvelopeCache = $this->getFetchValue(Enumerations\FetchType::ENVELOPE);
        }

        return $this->aEnvelopeCache;
    }

    /**
     * @param int   $iIndex
     * @param mixed $mNullResult = null
     *
     * @return mixed
     */
    public function getFetchEnvelopeValue($iIndex, $mNullResult) {
        return self::findEnvelopeIndex($this->getEnvelope(), $iIndex, $mNullResult);
    }

    /**
     * @param int    $iIndex
     * @param string $sParentCharset = \MailSo\Base\Enumerations\Charset::ISO_8859_1
     *
     * @return null|\CEmail_Client_Mime_EmailCollection
     */
    public function getFetchEnvelopeEmailCollection($iIndex, $sParentCharset = \MailSo\Base\Enumerations\Charset::ISO_8859_1) {
        $oResult = null;
        $aEmails = $this->GetFetchEnvelopeValue($iIndex, null);
        if (is_array($aEmails) && 0 < count($aEmails)) {
            $oResult = \CEmail_Client_Mime_EmailCollection::NewInstance();
            foreach ($aEmails as $aEmailItem) {
                if (is_array($aEmailItem) && 4 === count($aEmailItem)) {
                    $sDisplayName = \CEmail_Client_Utils::decodeHeaderValue(
                        self::findEnvelopeIndex($aEmailItem, 0, ''),
                        $sParentCharset
                    );

                    $sRemark = \CEmail_Client_Utils::decodeHeaderValue(
                        self::findEnvelopeIndex($aEmailItem, 1, ''),
                        $sParentCharset
                    );

                    $sLocalPart = self::findEnvelopeIndex($aEmailItem, 2, '');
                    $sDomainPart = self::findEnvelopeIndex($aEmailItem, 3, '');

                    if (0 < strlen($sLocalPart) && 0 < strlen($sDomainPart)) {
                        $oResult->Add(
                            \CEmail_Client_Mime_Email::newInstance(
                                $sLocalPart . '@' . $sDomainPart,
                                $sDisplayName,
                                $sRemark
                            )
                        );
                    }
                }
            }
        }

        return $oResult;
    }

    /**
     * @param string $sRfc822SubMimeIndex = ''
     *
     * @return null|\CEmail_Client_Imap_BodyStructure
     */
    public function GetFetchBodyStructure($sRfc822SubMimeIndex = '') {
        $oBodyStructure = null;
        $aBodyStructureArray = $this->GetFetchValue(Enumerations\FetchType::BODYSTRUCTURE);

        if (is_array($aBodyStructureArray)) {
            if (0 < strlen($sRfc822SubMimeIndex)) {
                $oBodyStructure = BodyStructure::newInstanceFromRfc822SubPart($aBodyStructureArray, $sRfc822SubMimeIndex);
            } else {
                $oBodyStructure = BodyStructure::newInstance($aBodyStructureArray);
            }
        }

        return $oBodyStructure;
    }

    /**
     * @param string $sFetchItemName
     *
     * @return mixed
     */
    public function GetFetchValue($sFetchItemName) {
        $mReturn = null;
        $bNextIsValue = false;

        if (Enumerations\FetchType::INDEX === $sFetchItemName) {
            $mReturn = $this->oImapResponse->ResponseList[1];
        } elseif (isset($this->oImapResponse->ResponseList[3]) && \is_array($this->oImapResponse->ResponseList[3])) {
            foreach ($this->oImapResponse->ResponseList[3] as $mItem) {
                if (is_string($mItem) && preg_match("/(BODY\[(.*?)\])<0>/i", $mItem, $aMatches)) {
                    $mItem = $aMatches[1];
                }

                if ($bNextIsValue) {
                    $mReturn = $mItem;

                    break;
                }

                if ($sFetchItemName === $mItem) {
                    $bNextIsValue = true;
                }
            }
        }

        return $mReturn;
    }

    /**
     * @param string $sRfc822SubMimeIndex = ''
     *
     * @return string
     */
    public function GetHeaderFieldsValue($sRfc822SubMimeIndex = '') {
        $sReturn = '';
        $bNextIsValue = false;

        $sRfc822SubMimeIndex = 0 < \strlen($sRfc822SubMimeIndex) ? '' . $sRfc822SubMimeIndex . '.' : '';

        if (isset($this->oImapResponse->ResponseList[3]) && \is_array($this->oImapResponse->ResponseList[3])) {
            foreach ($this->oImapResponse->ResponseList[3] as $mItem) {
                if ($bNextIsValue) {
                    $sReturn = (string) $mItem;

                    break;
                }

                if (\is_string($mItem) && (
                    $mItem === 'BODY[' . $sRfc822SubMimeIndex . 'HEADER]'
                    || 0 === \strpos($mItem, 'BODY[' . $sRfc822SubMimeIndex . 'HEADER.FIELDS')
                    || $mItem === 'BODY[' . $sRfc822SubMimeIndex . 'MIME]'
                )) {
                    $bNextIsValue = true;
                }
            }
        }

        return $sReturn;
    }

    private static function findFetchUidAndSize($aList) {
        $bUid = false;
        $bSize = false;
        if (is_array($aList)) {
            foreach ($aList as $mItem) {
                if (\CEmail_Client_Imap_Enumerations\FetchType::UID === $mItem) {
                    $bUid = true;
                } elseif (\CEmail_Client_Imap_Enumerations\FetchType::RFC822_SIZE === $mItem) {
                    $bSize = true;
                }
            }
        }

        return $bUid && $bSize;
    }

    /**
     * @param \CEmail_Client_Imap_Response $oImapResponse
     *
     * @return bool
     */
    public static function IsValidFetchImapResponse($oImapResponse) {
        return
            $oImapResponse
            && true !== $oImapResponse->IsStatusResponse
            && \CEmail_Client_Imap_Enumerations\ResponseType::UNTAGGED === $oImapResponse->ResponseType
            && 3 < count($oImapResponse->ResponseList) && 'FETCH' === $oImapResponse->ResponseList[2]
            && is_array($oImapResponse->ResponseList[3]);
    }

    /**
     * @param \CEmail_Client_Imap_Response $oImapResponse
     *
     * @return bool
     */
    public static function IsNotEmptyFetchImapResponse($oImapResponse) {
        return
            $oImapResponse
            && self::IsValidFetchImapResponse($oImapResponse)
            && isset($oImapResponse->ResponseList[3])
            && self::findFetchUidAndSize($oImapResponse->ResponseList[3]);
    }

    /**
     * @param array $aEnvelope
     * @param int   $iIndex
     * @param mixed $mNullResult = null
     *
     * @return mixed
     */
    private static function findEnvelopeIndex($aEnvelope, $iIndex, $mNullResult) {
        return (isset($aEnvelope[$iIndex]) && 'NIL' !== $aEnvelope[$iIndex] && '' !== $aEnvelope[$iIndex])
            ? $aEnvelope[$iIndex] : $mNullResult;
    }
}
