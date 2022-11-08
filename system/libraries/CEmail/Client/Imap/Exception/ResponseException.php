<?php
class CEmail_Client_Imap_Exception_ResponseException extends \CEmail_Client_Imap_Exception {
    /**
     * @var array
     */
    private $aResponses;

    /**
     * @param array      $aResponses = array
     * @param string     $sMessage   = ''
     * @param int        $iCode      = 0
     * @param \Exception $oPrevious  = null
     */
    public function __construct($aResponses = [], $sMessage = '', $iCode = 0, $oPrevious = null) {
        if (is_array($aResponses)) {
            $this->aResponses = $aResponses;

            if (empty($sMessage)) {
                foreach ($this->getResponses() as $oResponse) {
                    $sMessage .= $oResponse->toLine();
                }
            }
        }

        parent::__construct($sMessage, $iCode, $oPrevious);
    }

    /**
     * @return array
     */
    public function getResponses() {
        return $this->aResponses;
    }

    /**
     * @return null|\CEmail_Client_Imap_Response
     */
    public function getLastResponse() {
        return 0 < count($this->aResponses) ? $this->aResponses[count($this->aResponses) - 1] : null;
    }
}
