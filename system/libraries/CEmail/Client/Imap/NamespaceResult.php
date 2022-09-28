<?php
class CEmail_Client_Imap_NamespaceResult {
    /**
     * @var string
     */
    private $sPersonal;

    /**
     * @var string
     */
    private $sPersonalDelimiter;

    /**
     * @var string
     */
    private $sOtherUser;

    /**
     * @var string
     */
    private $sOtherUserDelimiter;

    /**
     * @var string
     */
    private $sShared;

    /**
     * @var string
     */
    private $sSharedDelimiter;

    private function __construct() {
        $this->sPersonal = '';
        $this->sPersonalDelimiter = '';
        $this->sOtherUser = '';
        $this->sOtherUserDelimiter = '';
        $this->sShared = '';
        $this->sSharedDelimiter = '';
    }

    /**
     * @return \CEmail_Client_Imap_NamespaceResult
     */
    public static function newInstance() {
        return new self();
    }

    /**
     * @param \CEmail_Client_Imap_Response $oImapResponse
     *
     * @return \CEmail_Client_Imap_NamespaceResult
     */
    public function initByImapResponse($oImapResponse) {
        if ($oImapResponse && $oImapResponse instanceof \CEmail_Client_Imap_Response) {
            if (isset($oImapResponse->ResponseList[2][0])
                && \is_array($oImapResponse->ResponseList[2][0])
                && 2 <= \count($oImapResponse->ResponseList[2][0])
            ) {
                $this->sPersonal = $oImapResponse->ResponseList[2][0][0];
                $this->sPersonalDelimiter = $oImapResponse->ResponseList[2][0][1];

                $this->sPersonal = 'INBOX' . $this->sPersonalDelimiter === \substr(\strtoupper($this->sPersonal), 0, 6)
                    ? 'INBOX' . $this->sPersonalDelimiter . \substr($this->sPersonal, 6) : $this->sPersonal;
            }

            if (isset($oImapResponse->ResponseList[3][0])
                && \is_array($oImapResponse->ResponseList[3][0])
                && 2 <= \count($oImapResponse->ResponseList[3][0])
            ) {
                $this->sOtherUser = $oImapResponse->ResponseList[3][0][0];
                $this->sOtherUserDelimiter = $oImapResponse->ResponseList[3][0][1];

                $this->sOtherUser = 'INBOX' . $this->sOtherUserDelimiter === \substr(\strtoupper($this->sOtherUser), 0, 6)
                    ? 'INBOX' . $this->sOtherUserDelimiter . \substr($this->sOtherUser, 6) : $this->sOtherUser;
            }

            if (isset($oImapResponse->ResponseList[4][0])
                && \is_array($oImapResponse->ResponseList[4][0])
                && 2 <= \count($oImapResponse->ResponseList[4][0])
            ) {
                $this->sShared = $oImapResponse->ResponseList[4][0][0];
                $this->sSharedDelimiter = $oImapResponse->ResponseList[4][0][1];

                $this->sShared = 'INBOX' . $this->sSharedDelimiter === \substr(\strtoupper($this->sShared), 0, 6)
                    ? 'INBOX' . $this->sSharedDelimiter . \substr($this->sShared, 6) : $this->sShared;
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPersonalNamespace() {
        return $this->sPersonal;
    }

    /**
     * @return string
     */
    public function getPersonalNamespaceDelimiter() {
        return $this->sPersonalDelimiter;
    }
}
