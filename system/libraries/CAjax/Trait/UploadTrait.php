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
            throw new CAjax_Exception_UploadNotAllowedException('Not Allowed X_X');
        }
        if (cstr::startsWith($ext, $this->getBlacklistedExtension())) {
            throw new CAjax_Exception_UploadNotAllowedException('Not Allowed X_X');
        }

        if ($allowedExtension) {
            if (!in_array(strtolower($ext), $allowedExtension)) {
                throw new CAjax_Exception_UploadNotAllowedException('Extension not allowed, allowed extension: ' . implode(', ', $allowedExtension));
            }
        }
    }

    public function getType() {
        if ($this instanceof CAjax_Engine_ImgUpload) {
            return CAjax_FileAjax::TYPE_IMAGE;
        }

        return CAjax_FileAjax::TYPE_FILE;
    }

    /**
     * @return string
     */
    public function getTemporaryFolderName() {
        return CF::config('temporary.upload.' . $this->getType(), $this->getType() == CAjax_FileAjax::TYPE_IMAGE ? 'imgupload' : 'fileupload');
    }

    public function getInfoTemporaryFolderName() {
        return CF::config('temporary.upload.' . $this->getType() . '_info', $this->getType() == CAjax_FileAjax::TYPE_IMAGE ? 'imguploadinfo' : 'fileuploadinfo');
    }
}
