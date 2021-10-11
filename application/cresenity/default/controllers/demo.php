<?php

class Controller_Demo extends \Cresenity\Demo\Controller {
    public function index() {
        return c::redirect('demo/dashboard');
    }
}
