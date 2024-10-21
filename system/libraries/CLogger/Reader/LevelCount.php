<?php

class CLogger_Reader_LevelCount {
    public CLogger_Level $level;

    public int $count = 0;

    public bool $selected = false;

    public function __construct(
        CLogger_Level $level,
        int $count = 0,
        bool $selected = false
    ) {
        $this->level = $level;
        $this->count = $count;
        $this->selected = $selected;
    }
}
