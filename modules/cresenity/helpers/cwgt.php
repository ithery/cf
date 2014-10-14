<?php

class cwgt {

    public static function get_all_widget() {
        $app = CApp::instance();
        $file = DOCROOT . "config/widget/" . $app->code() . "/widget.php";
        $result = array();
        if (file_exists($file)) {
            require_once $file;
            $result = $config["cwidget"];
        }
        return $result;
    }

}
