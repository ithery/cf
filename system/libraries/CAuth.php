<?php

class CAuth {
    public static function factory() {
        return CAuth_Manager::instance();
    }
}
