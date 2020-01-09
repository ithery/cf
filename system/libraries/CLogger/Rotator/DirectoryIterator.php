<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CLogger_Rotator_DirectoryIterator extends \DirectoryIterator {

    /**
     * Filename format to match files against
     *
     * @var FilenameFormat
     */
    protected $filenameFormat;

    /**
     * Set the filename format for this iterator
     *
     * @param FilenameFormat $filenameFormat
     */
    public function setFilenameFormat(CLogger_Rotator_FilenameFormat $filenameFormat) {
        $this->filenameFormat = $filenameFormat;
    }

    /**
     * Is the current filename a match for the filename format we're looking for?
     *
     * @return bool
     */
    public function isMatch() {
        return (bool) preg_match($this->filenameFormat->getFilenameRegex(), $this->getFilename());
    }

    /**
     * Does the filename format we're looking for contain a date?
     *
     * @return bool
     */
    public function hasDate() {
        return $this->filenameFormat->hasDateFormat();
    }

    /**
     * Return date contained within the current filename
     *
     * @return DateTime or false on failure
     */
    public function getFilenameDate() {
        if (!$this->hasDate()) {
            return false;
        }

        if (preg_match($this->filenameFormat->getFilenameRegex(), $this->getFilename(), $m)) {
            return \DateTime::createFromFormat($this->filenameFormat->getDateFormat(), $m[1]);
        }
        return false;
    }

    /**
     * Return rotated filename
     *
     * @param int $num Rotation number
     * @return string
     */
    public function getRotatedFilename($num) {
        return $this->getBasename() . '.' . $num;
    }

}
