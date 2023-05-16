<?php

class CLogger_Reader_Event_LogFileDeleted {
    use CEvent_Trait_Dispatchable;

    /**
     * @var CLogger_Reader_LogFile
     */
    public $file;

    public function __construct(CLogger_Reader_LogFile $file) {
        $this->file = $file;
    }
}
