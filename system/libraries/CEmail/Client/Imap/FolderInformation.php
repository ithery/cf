<?php

class CEmail_Client_Imap_FolderInformation {
    /**
     * @var string
     */
    public $folderName;

    /**
     * @var bool
     */
    public $isWritable;

    /**
     * @var array
     */
    public $flags;

    /**
     * @var array
     */
    public $permanentFlags;

    /**
     * @var int
     */
    public $exists;

    /**
     * @var int
     */
    public $recent;

    /**
     * @var string
     */
    public $uidvalidity;

    /**
     * @var int
     */
    public $unread;

    /**
     * @var string
     */
    public $uidnext;

    /**
     * @param string $sFolderName
     * @param bool   $bIsWritable
     */
    private function __construct($sFolderName, $bIsWritable) {
        $this->folderName = $sFolderName;
        $this->isWritable = $bIsWritable;
        $this->exists = null;
        $this->Recent = null;
        $this->flags = [];
        $this->permanentFlags = [];

        $this->unread = null;
        $this->uidnext = null;
    }

    /**
     * @param string $sFolderName
     * @param bool   $bIsWritable
     *
     * @return \CEmail_Client_Imap_FolderInformation
     */
    public static function newInstance($sFolderName, $bIsWritable) {
        return new self($sFolderName, $bIsWritable);
    }

    /**
     * @param string $sFlag
     *
     * @return bool
     */
    public function isFlagSupported($sFlag) {
        return in_array('\\*', $this->permanentFlags) || in_array($sFlag, $this->permanentFlags);
    }
}
