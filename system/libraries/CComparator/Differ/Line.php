<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class CComparator_Differ_Line {

    const ADDED = 1;
    const REMOVED = 2;
    const UNCHANGED = 3;

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $content;

    public function __construct($type = self::UNCHANGED, $content = '') {
        $this->type = $type;
        $this->content = $content;
    }

    public function getContent() {
        return $this->content;
    }

    public function getType() {
        return $this->type;
    }

}
