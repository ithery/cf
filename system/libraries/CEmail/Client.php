<?php
/**
 * @see https://github.com/afterlogic/MailSo
 */
class CEmail_Client {
    const SECURITY_TYPE_NONE = 0;

    const SECURITY_TYPE_SSL = 1;

    const SECURITY_TYPE_STARTTLS = 2;

    const SECURITY_TYPE_AUTO_DETECT = 9;

    protected $iconv;

    protected $mbstring;

    protected $fixIconvByMbstring;

    protected $messageListFastSimpleSearch;

    protected $messageListCountLimitTrigger;

    protected $messageListDateFilter;

    protected $largeThreadLimit;

    protected $logSimpleLiterals;

    protected $preferStartTlsIfAutoDetect;

    protected $systemLogger;

    public function __construct() {
        $this->iconv = true;
        $this->mbstring = true;
        $this->fixIconvByMbstring = true;
        $this->messageListFastSimpleSearch = true;
        $this->messageListCountLimitTrigger = 0;
        $this->messageListDateFilter = 0;
        $this->largeThreadLimit = 100;
        $this->logSimpleLiterals = false;
        $this->preferStartTlsIfAutoDetect = true;
        $this->systemLogger = null;
    }

    public function imap() {
        return CEmail_Client_ImapClient::newInstance();
    }
}
