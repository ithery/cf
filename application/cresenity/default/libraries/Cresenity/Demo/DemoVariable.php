<?php

namespace Cresenity\Demo;

class DemoVariable {
    public static function username() {
        return 'CF Demo';
    }

    public static function theme() {
        return \c::session()->get('theme', 'cresenity-demo');
    }

    public static function themeData() {
        return [
            'cresenity-demo' => 'Bootstrap 4',
            'cresenity-demo-bs5' => 'Bootstrap 5',

        ];
    }

    public static function themeLabel() {
        return \carr::get(self::themeData(), self::theme());
    }
}
