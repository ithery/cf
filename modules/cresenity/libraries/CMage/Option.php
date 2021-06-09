<?php

class CMage_Option {
    protected $title;

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }
}
