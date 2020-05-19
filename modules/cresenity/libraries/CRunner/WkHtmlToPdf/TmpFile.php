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
 * @author Michael Härtl <haertl.mike@gmail.com>
 * @license http://www.opensource.org/licenses/MIT
 */
class CRunner_WkHtmlToPdf_TmpFile extends CTemporary_LocalFile {

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
    public function __construct($content, $suffix = null) {

        parent::__construct($content, 'wkHtmlToPdf', $suffix, $this->delete);
    }

}
