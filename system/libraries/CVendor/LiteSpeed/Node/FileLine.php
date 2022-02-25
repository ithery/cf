<?php

class CVendor_LiteSpeed_Node_FileLine {
    public $note0;  // begin notes

    public $note1 = '';  // end notes

    public $fid;

    public $fline0;

    public $fline1;

    public function __construct($file_id, $from_line, $to_line, $comment) {
        $this->fid = $file_id;
        $this->fline0 = $from_line;
        $this->fline1 = $to_line;
        $this->note0 = $comment;
    }

    public function addEndComment($note1) {
        $this->note1 .= "${note1}\n";
    }

    public function debugStr() {
        return sprintf('fid=%s from line %s to %s', $this->_fid, $this->_fline0, $this->_fline1);
    }
}
