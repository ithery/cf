<?php

interface CDebug_Contract_RequestIdGeneratorInterface {
    /**
     * Generates a unique id for the current request.  If called repeatedly, a new unique id must
     * always be returned on each call to generate() - even across different object instances.
     *
     * To avoid any potential confusion in ID --> value maps, the returned value must be
     * guaranteed to not be all-numeric.
     *
     * @return string
     */
    public function generate();
}
