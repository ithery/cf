<?php

class CDocs {
    /**
     * @return CDocs_PhpDocumentor
     */
    public static function phpDocumentor() {
        return CDocs_PhpDocumentor::instance();
    }

    /**
     * @return CDocs_ApiGen
     */
    public static function apiGen() {
        return CDocs_ApiGen::instance();
    }
}
