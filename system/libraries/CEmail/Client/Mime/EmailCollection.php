<?php

class CEmail_Client_Mime_EmailCollection extends \CEmail_Client_AbstractCollection {
    /**
     * @param string $sEmailAddresses = ''
     */
    protected function __construct($sEmailAddresses = '') {
        parent::__construct();

        if (0 < \strlen($sEmailAddresses)) {
            $this->parseEmailAddresses($sEmailAddresses);
        }
    }

    /**
     * @param string $sEmailAddresses = ''
     *
     * @return \CEmail_Client_Mime_EmailCollection
     */
    public static function newInstance($sEmailAddresses = '') {
        return new self($sEmailAddresses);
    }

    /**
     * @param string $sEmailAddresses
     *
     * @return \CEmail_Client_Mime_EmailCollection
     */
    public static function parse($sEmailAddresses) {
        return self::newInstance($sEmailAddresses);
    }

    /**
     * @return array
     */
    public function toArray() {
        $aReturn = $aEmails = [];
        $aEmails = &$this->GetAsArray();
        foreach ($aEmails as /* @var $oEmail \CEmail_Client_Mime_Email */ $oEmail) {
            $aReturn[] = $oEmail->toArray();
        }

        return $aReturn;
    }

    /**
     * @param \CEmail_Client_Mime_EmailCollection $oEmails
     *
     * @return \CEmail_Client_Mime_EmailCollection
     */
    public function mergeWithOtherCollection(CEmail_Client_Mime_EmailCollection $oEmails) {
        $aEmails = &$oEmails->GetAsArray();
        foreach ($aEmails as /* @var $oEmail \CEmail_Client_Mime_Email */ $oEmail) {
            $this->Add($oEmail);
        }

        return $this;
    }

    /**
     * @return \CEmail_Client_Mime_EmailCollection
     */
    public function unique() {
        $aCache = [];
        $aReturn = [];

        $aEmails = &$this->GetAsArray();
        foreach ($aEmails as /* @var $oEmail \CEmail_Client_Mime_Email */ $oEmail) {
            $sEmail = $oEmail->GetEmail();
            if (!isset($aCache[$sEmail])) {
                $aCache[$sEmail] = true;
                $aReturn[] = $oEmail;
            }
        }

        $this->SetAsArray($aReturn);

        return $this;
    }

    /**
     * @param bool $bConvertSpecialsName = false
     * @param bool $bIdn                 = false
     *
     * @return string
     */
    public function toString($bConvertSpecialsName = false, $bIdn = false) {
        $aReturn = $aEmails = [];
        $aEmails = &$this->GetAsArray();
        foreach ($aEmails as /* @var $oEmail \CEmail_Client_Mime_Email */ $oEmail) {
            $aReturn[] = $oEmail->ToString($bConvertSpecialsName, $bIdn);
        }

        return \implode(', ', $aReturn);
    }

    /**
     * @param string $sRawEmails
     *
     * @return \CEmail_Client_Mime_EmailCollection
     */
    private function parseEmailAddresses($sRawEmails) {
        $this->clear();

        $sWorkingRecipients = \trim($sRawEmails);

        if (0 === \strlen($sWorkingRecipients)) {
            return $this;
        }

        $iEmailStartPos = 0;
        $iEmailEndPos = 0;

        $bIsInQuotes = false;
        $sChQuote = '"';
        $bIsInAngleBrackets = false;
        $bIsInBrackets = false;

        $iCurrentPos = 0;

        $sWorkingRecipientsLen = \strlen($sWorkingRecipients);

        while ($iCurrentPos < $sWorkingRecipientsLen) {
            switch ($sWorkingRecipients[$iCurrentPos]) {
                case '\'':
                case '"':
                    if (!$bIsInQuotes) {
                        $sChQuote = $sWorkingRecipients[$iCurrentPos];
                        $bIsInQuotes = true;
                    } elseif ($sChQuote == $sWorkingRecipients[$iCurrentPos]) {
                        $bIsInQuotes = false;
                    }

                    break;

                case '<':
                    if (!$bIsInAngleBrackets) {
                        $bIsInAngleBrackets = true;
                        if ($bIsInQuotes) {
                            $bIsInQuotes = false;
                        }
                    }

                    break;

                case '>':
                    if ($bIsInAngleBrackets) {
                        $bIsInAngleBrackets = false;
                    }

                    break;

                case '(':
                    if (!$bIsInBrackets) {
                        $bIsInBrackets = true;
                    }

                    break;

                case ')':
                    if ($bIsInBrackets) {
                        $bIsInBrackets = false;
                    }

                    break;

                case ',':
                case ';':
                    if (!$bIsInAngleBrackets && !$bIsInBrackets && !$bIsInQuotes) {
                        $iEmailEndPos = $iCurrentPos;

                        try {
                            $this->add(
                                \CEmail_Client_Mime_Email::parse(\substr($sWorkingRecipients, $iEmailStartPos, $iEmailEndPos - $iEmailStartPos))
                            );

                            $iEmailStartPos = $iCurrentPos + 1;
                        } catch (\CEmail_Client_Exception_InvalidArgumentException $oException) {
                        }
                    }

                    break;
            }

            $iCurrentPos++;
        }

        if ($iEmailStartPos < $iCurrentPos) {
            try {
                $this->Add(
                    \CEmail_Client_Mime_Email::Parse(\substr($sWorkingRecipients, $iEmailStartPos, $iCurrentPos - $iEmailStartPos))
                );
            } catch (\CEmail_Client_Exception_InvalidArgumentException $oException) {
            }
        }

        return $this;
    }
}
