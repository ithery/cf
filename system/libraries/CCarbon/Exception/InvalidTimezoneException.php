<?php
class CCarbon_Exception_InvalidTimezoneException extends InvalidArgumentException {
    public static function create() {
        return new self('Invalid Timezone');
    }
}
