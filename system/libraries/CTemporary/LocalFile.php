<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * File
 *
 * A convenience class for temporary files.
 *
 */
class CTemporary_LocalFile {

    /**
     * @var bool whether to delete the tmp file when it's no longer referenced
     * or when the request ends.  Default is `true`.
     */
    protected $delete = true;

    /**
     * @var string the name of this file
     */
    protected $_fileName;

    /**
     * Constructor
     *
     * @param string $content the tmp file content
     * @param string|null $suffix the optional suffix for the tmp file
     * @param string|null $prefix the optional prefix for the tmp file. If null
     * 'php_tmpfile_' is used.
     * @param string|null $directory directory where the file should be
     * created. Autodetected if not provided.
     */
    public function __construct($content, $folder = null, $suffix = null, $delete = true) {


        $this->delete = $delete;


        $filename = date('Ymd') . cutils::randmd5();
        if ($suffix !== null) {
            $filename .= $suffix;
        }

        if ($folder == null) {
            $folder = 'common';
        }

        $this->_fileName = CTemporary::local()->put($content, $folder, $filename);
    }

    /**
     * Delete tmp file on shutdown if `$delete` is `true`
     */
    public function __destruct() {
        if ($this->delete) {
            CTemporary::local()->delete($this->_fileName);
        }
    }

    /**
     * Send tmp file to client, either inline or as download
     *
     * @param string|null $filename the filename to send. If empty, the file is
     * streamed inline.
     * @param string $contentType the Content-Type header
     * @param bool $inline whether to force inline display of the file, even if
     * filename is present.
     */
    public function send($filename = null, $contentType, $inline = false) {
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: ' . $contentType);
        header('Content-Transfer-Encoding: binary');

        //#11 Undefined index: HTTP_USER_AGENT
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        // #84: Content-Length leads to "network connection was lost" on iOS
        $isIOS = preg_match('/i(phone|pad|pod)/i', $userAgent);
        if (!$isIOS) {
            header('Content-Length: ' . filesize($this->_fileName));
        }

        if ($filename !== null || $inline) {
            $disposition = $inline ? 'inline' : 'attachment';
            $encodedFilename = rawurlencode($filename);
            header(
                    "Content-Disposition: $disposition; " .
                    "filename=\"$filename\"; " .
                    "filename*=UTF-8''$encodedFilename"
            );
        }


        readfile($this->getFileName());
    }

    /**
     * @return string the full file name
     */
    public function getFileName() {

        $filename = CTemporary::local()->getDriver()->getAdapter()->getPathPrefix() . $this->_fileName;
        return $filename;
    }

    /**
     * @return string the full file name
     */
    public function __toString() {
        return $this->getFileName();
    }

    public function delete() {
        $filename = $this->getFileName();
        if (file_exists($filename)) {
            return @unlink($filename);
        }
        return false;
    }

}
