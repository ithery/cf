<?php

class CTemplate_Blade_Directive {
    public static function block($expression) {
        return '{!! $this->block(' . $expression . ') !!}';
    }
}
