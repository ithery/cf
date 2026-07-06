<?php

class Controller_Demo_Report_Ui extends \Cresenity\Demo\Controller {
    use CReport_UIBuilder_Trait_UIBuilderTrait;
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        return $this->ui();
    }
}
