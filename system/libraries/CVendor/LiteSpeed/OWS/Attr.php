<?php

class CVendor_LiteSpeed_OWS_Attr extends CVendor_LiteSpeed_AttrBase {
    public $cyberpanelBlocked = false;

    public function blockedVersion() {
        if ($this->cyberpanelBlocked) {
            if (CVendor_LiteSpeed_PathTool::isCyberPanel()) {
                return 'Locked due to CyberPanel';
            }
        }

        // no other block
        return false;
    }

    public function bypassSavePost() {
        return $this->isFlagOn(static::BM_NOEDIT);
    }
}
