<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_Asset_Container_Theme extends CManager_Asset_Container {
    public function isCompiled() {
        return true;
    }
}
