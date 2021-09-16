<?php

/**
 * File
 *
 * A convenience class for temporary files.
 */
class CRunner_WkHtmlToPdf_TmpFile extends CTemporary_LocalFile {
    /**
     * Constructor
     *
     * @param mixed      $content
     * @param null|mixed $suffix
     */
    public function __construct($content, $suffix = null) {
        parent::__construct($content, 'wkHtmlToPdf', $suffix, $this->delete);
    }
}
