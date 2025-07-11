<?php

trait CAjax_Trait_UploadTrait {
    /**
     * @return CStorage_Adapter
     */
    public function getDisk() {
        return CTemporary::publicDisk();
    }

    public function getBlacklistedExtension() {
        return ['php', 'sh', 'htm', 'pht'];
    }

    public function checkExtension($ext, $allowedExtension = null) {
        if (in_array($ext, $this->getBlacklistedExtension())) {
            die('Not Allowed X_X');
        }
        if (cstr::startsWith($ext, $this->getBlacklistedExtension())) {
            die('Not Allowed X_X');
        }

        if ($allowedExtension) {
            if (!in_array(strtolower($ext), $allowedExtension)) {
                die('Not Allowed X_X');
            }
        }
    }
}
