<?php

class CTemplate_Blade_Directive {
    public static function block($expression) {
        return '{!! $this->block(' . $expression . ') !!}';
    }

    public static function template($expression) {
        return '{!! $this->template(' . $expression . ') !!}';
    }
}
