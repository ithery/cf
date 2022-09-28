<?php

class CPeriod_Exception_InvalidTimezoneException extends InvalidArgumentException {
    /**
     * @return self
     */
    public static function create() {
        return new self('Invalid Timezone');
    }
}
