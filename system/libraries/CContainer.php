<?php

defined('SYSPATH') or die('No direct access allowed.');

class CContainer {
    /**
     * @return CContainer_Container
     */
    public static function getInstance() {
        return CContainer_Container::getInstance();
    }

    public static function createContainer() {
        return new CContainer_Container();
    }
}
