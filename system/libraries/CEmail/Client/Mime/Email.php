<?php

class CEmail_Client_Mime_Email {
    /**
     * @var string
     */
    private $sDisplayName;

    /**
     * @var string
     */
    private $sEmail;

    /**
     * @var string
     */
    private $sRemark;

    /**
     * @param string $sEmail
     * @param string $sDisplayName = ''
     * @param string $sRemark      = ''
     *
     * @throws \CEmail_Client_Exceptions\InvalidArgumentException
     */
    private function __construct($sEmail, $sDisplayName = '', $sRemark = '') {
        if (!\CBase_Validation::notEmptyString($sEmail, true)) {
            throw new \CEmail_Client_Exception_InvalidArgumentException();
        }

        $this->sEmail = \CEmail_Client_Utils::idnToAscii(\trim($sEmail), true);
        $this->sDisplayName = \trim($sDisplayName);
        $this->sRemark = \trim($sRemark);
    }

    /**
     * @param string $sEmail
     * @param string $sDisplayName = ''
     * @param string $sRemark      = ''
     *
     * @throws \CEmail_Client_Exceptions\InvalidArgumentException
     *
     * @return \CEmail_Client_Mime_Email
     */
    public static function newInstance($sEmail, $sDisplayName = '', $sRemark = '') {
        return new self($sEmail, $sDisplayName, $sRemark);
    }

    /**
     * @param string $sEmailAddress
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     *
     * @return \CEmail_Client_Mime_Email
     */
    public static function parse($sEmailAddress) {
        if (!\CBase_Validation::notEmptyString($sEmailAddress, true)) {
            throw new \CEmail_Client_Exception_InvalidArgumentException();
        }

        $sName = '';
        $sEmail = '';
        $sComment = '';

        $bInName = false;
        $bInAddress = false;
        $bInComment = false;

        $iStartIndex = 0;
        $iEndIndex = 0;
        $iCurrentIndex = 0;

        while ($iCurrentIndex < \strlen($sEmailAddress)) {
            switch ($sEmailAddress[$iCurrentIndex]) {
                case '"':
                    //$sQuoteChar = $sEmailAddress{$iCurrentIndex};
                    if ((!$bInName) && (!$bInAddress) && (!$bInComment)) {
                        $bInName = true;
                        $iStartIndex = $iCurrentIndex;
                    } elseif ((!$bInAddress) && (!$bInComment)) {
                        $iEndIndex = $iCurrentIndex;
                        $sName = \substr($sEmailAddress, $iStartIndex + 1, $iEndIndex - $iStartIndex - 1);
                        $sEmailAddress = \substr_replace($sEmailAddress, '', $iStartIndex, $iEndIndex - $iStartIndex + 1);
                        $iEndIndex = 0;
                        $iCurrentIndex = 0;
                        $iStartIndex = 0;
                        $bInName = false;
                    }

                    break;
                case '<':
                    if ((!$bInName) && (!$bInAddress) && (!$bInComment)) {
                        if ($iCurrentIndex > 0 && \strlen($sName) === 0) {
                            $sName = \substr($sEmailAddress, 0, $iCurrentIndex);
                        }

                        $bInAddress = true;
                        $iStartIndex = $iCurrentIndex;
                    }

                    break;
                case '>':
                    if ($bInAddress) {
                        $iEndIndex = $iCurrentIndex;
                        $sEmail = \substr($sEmailAddress, $iStartIndex + 1, $iEndIndex - $iStartIndex - 1);
                        $sEmailAddress = \substr_replace($sEmailAddress, '', $iStartIndex, $iEndIndex - $iStartIndex + 1);
                        $iEndIndex = 0;
                        $iCurrentIndex = 0;
                        $iStartIndex = 0;
                        $bInAddress = false;
                    }

                    break;
                case '(':
                    if ((!$bInName) && (!$bInAddress) && (!$bInComment)) {
                        $bInComment = true;
                        $iStartIndex = $iCurrentIndex;
                    }

                    break;
                case ')':
                    if ($bInComment) {
                        $iEndIndex = $iCurrentIndex;
                        $sComment = \substr($sEmailAddress, $iStartIndex + 1, $iEndIndex - $iStartIndex - 1);
                        $sEmailAddress = \substr_replace($sEmailAddress, '', $iStartIndex, $iEndIndex - $iStartIndex + 1);
                        $iEndIndex = 0;
                        $iCurrentIndex = 0;
                        $iStartIndex = 0;
                        $bInComment = false;
                    }

                    break;
                case '\\':
                    $iCurrentIndex++;

                    break;
            }

            $iCurrentIndex++;
        }

        if (\strlen($sEmail) === 0) {
            $aRegs = [''];
            if (\preg_match('/[^@\s]+@\S+/i', $sEmailAddress, $aRegs) && isset($aRegs[0])) {
                $sEmail = $aRegs[0];
            } else {
                $sName = $sEmailAddress;
            }
        }

        if ((\strlen($sEmail) > 0) && (\strlen($sName) == 0) && (\strlen($sComment) == 0)) {
            $sName = \str_replace($sEmail, '', $sEmailAddress);
        }

        $sEmail = \trim(\trim($sEmail), '<>');

        $sName = \CEmail_Client_Utils::customTrim(\trim($sName), '"'); //standard trim removes more than necessary
        //$sName = \trim(\trim($sName), '"');
        $sName = \trim($sName, '\'');
        $sComment = \trim(\trim($sComment), '()');

        // Remove backslash
        $sName = \preg_replace('/\\\\(.)/s', '$1', $sName);
        $sComment = \preg_replace('/\\\\(.)/s', '$1', $sComment);

        return CEmail_Client_Mime_Email::newInstance($sEmail, $sName, $sComment);
    }

    /**
     * @param bool $bIdn = false
     *
     * @return string
     */
    public function getEmail($bIdn = false) {
        return $bIdn ? \CEmail_Client_Utils::idnToUtf8($this->sEmail) : $this->sEmail;
    }

    /**
     * @return string
     */
    public function getDisplayName() {
        return $this->sDisplayName;
    }

    /**
     * @return string
     */
    public function getRemark() {
        return $this->sRemark;
    }

    /**
     * @return string
     */
    public function getAccountName() {
        return \CEmail_Client_Utils::GetAccountNameFromEmail($this->GetEmail(false));
    }

    /**
     * @param bool $bIdn = false
     *
     * @return string
     */
    public function getDomain($bIdn = false) {
        return \CEmail_Client_Utils::GetDomainFromEmail($this->GetEmail($bIdn));
    }

    /**
     * @param bool $bIdn = false
     *
     * @return array
     */
    public function toArray($bIdn = false) {
        return [$this->sDisplayName, $this->GetEmail($bIdn), $this->sRemark];
    }

    public function toResponseArray() {
        return [
            'DisplayName' => $this->sDisplayName,
            'Email' => $this->GetEmail()
        ];
    }

    /**
     * @param bool $bConvertSpecialsName = false
     * @param bool $bIdn                 = false
     *
     * @return string
     */
    public function toString($bConvertSpecialsName = false, $bIdn = false) {
        $sReturn = '';

        $sRemark = \str_replace(')', '\)', $this->sRemark);
        $sDisplayName = \str_replace('"', '\"', $this->sDisplayName);

        if ($bConvertSpecialsName) {
            $sDisplayName = 0 === \strlen($sDisplayName) ? '' : \CEmail_Client_Utils::encodeUnencodedValue(
                \CEmail_Client_Utils::ENCODING_BASE64_SHORT,
                $sDisplayName
            );

            $sRemark = 0 === \strlen($sRemark) ? '' : \CEmail_Client_Utils::encodeUnencodedValue(
                \CEmail_Client_Utils::ENCODING_BASE64_SHORT,
                $sRemark
            );
        }

        $sDisplayName = 0 === \strlen($sDisplayName) ? '' : '"' . $sDisplayName . '"';
        $sRemark = 0 === \strlen($sRemark) ? '' : '(' . $sRemark . ')';

        if (0 < \strlen($this->sEmail)) {
            $sReturn = $this->getEmail($bIdn);
            if (0 < \strlen($sDisplayName . $sRemark)) {
                $sReturn = $sDisplayName . ' <' . $sReturn . '> ' . $sRemark;
            }
        }

        return \trim($sReturn);
    }
}
