<?php

class CEmail_Client_Imap_Folder {
    const FOLDER_STATUS_MESSAGES = 'MESSAGES';

    const FOLDER_STATUS_RECENT = 'RECENT';

    const FOLDER_STATUS_UNSEEN = 'UNSEEN';

    const FOLDER_STATUS_UIDNEXT = 'UIDNEXT';

    const FOLDER_STATUS_UIDVALIDITY = 'UIDVALIDITY';

    /**
     * @var string
     */
    private $sNameRaw;

    /**
     * @var string
     */
    private $sFullNameRaw;

    /**
     * @var string
     */
    private $sDelimiter;

    /**
     * @var array
     */
    private $aFlags;

    /**
     * @var array
     */
    private $aFlagsLowerCase;

    /**
     * @var array
     */
    private $aExtended;

    /**
     * @param string|array $sFullNameRaw
     * @param string       $sDelimiter
     * @param array        $aFlags
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     */
    private function __construct($sFullNameRaw, $sDelimiter, array $aFlags) {
        if ('array' === gettype($sFullNameRaw) && 1 === count($sFullNameRaw) && 'string' === gettype($sFullNameRaw[0])) {
            $sFullNameRaw = '[' . $sFullNameRaw[0] . ']';
        }

        $this->sNameRaw = '';
        $this->sFullNameRaw = '';
        $this->sDelimiter = '';
        $this->aFlags = [];
        $this->aExtended = [];

        $sDelimiter = 'NIL' === \strtoupper($sDelimiter) ? '' : $sDelimiter;
        if (empty($sDelimiter)) {
            $sDelimiter = '.'; // default delimiter
        }

        if (!\is_array($aFlags)
            || !\is_string($sDelimiter) || 1 < \strlen($sDelimiter)
            || !\is_string($sFullNameRaw) || 0 === \strlen($sFullNameRaw)
        ) {
            throw new \CEmail_Client_Exception_InvalidArgumentException();
        }

        $this->sFullNameRaw = $sFullNameRaw;
        $this->sDelimiter = $sDelimiter;
        $this->aFlags = $aFlags;
        $this->aFlagsLowerCase = \array_map('strtolower', $this->aFlags);

        $this->sFullNameRaw = 'INBOX' . $this->sDelimiter === \substr(\strtoupper($this->sFullNameRaw), 0, 5 + \strlen($this->sDelimiter))
            ? 'INBOX' . \substr($this->sFullNameRaw, 5) : $this->sFullNameRaw;

        if ($this->IsInbox()) {
            $this->sFullNameRaw = 'INBOX';
        }

        $this->sNameRaw = $this->sFullNameRaw;
        if (0 < \strlen($this->sDelimiter)) {
            $aNames = \explode($this->sDelimiter, $this->sFullNameRaw);
            $this->sNameRaw = \end($aNames);
        }
    }

    /**
     * @param string $sFullNameRaw
     * @param string $sDelimiter   = '.'
     * @param array  $aFlags       = array()
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     *
     * @return \CEmail_Client_Imap_Folder
     */
    public static function newInstance($sFullNameRaw, $sDelimiter = '.', $aFlags = []) {
        return new self($sFullNameRaw, $sDelimiter, $aFlags);
    }

    /**
     * @return string
     */
    public function nameRaw() {
        return $this->sNameRaw;
    }

    /**
     * @return string
     */
    public function fullNameRaw() {
        return $this->sFullNameRaw;
    }

    /**
     * @return null|string
     */
    public function delimiter() {
        return $this->sDelimiter;
    }

    /**
     * @return array
     */
    public function flags() {
        return $this->aFlags;
    }

    /**
     * @return array
     */
    public function flagsLowerCase() {
        return $this->aFlagsLowerCase;
    }

    /**
     * @return bool
     */
    public function isSelectable() {
        return !\in_array('\noselect', $this->aFlagsLowerCase);
    }

    /**
     * @return bool
     */
    public function isInbox() {
        return 'INBOX' === \strtoupper($this->sFullNameRaw) || \in_array('\inbox', $this->aFlagsLowerCase);
    }

    /**
     * @param string $sName
     * @param mixed  $mData
     */
    public function setExtended($sName, $mData) {
        $this->aExtended[$sName] = $mData;
    }

    /**
     * @param string $sName
     *
     * @return mixed
     */
    public function getExtended($sName) {
        return isset($this->aExtended[$sName]) ? $this->aExtended[$sName] : null;
    }
}
