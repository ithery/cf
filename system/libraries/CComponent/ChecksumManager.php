<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_ChecksumManager {
    public function generate($fingerprint, $memo) {
        $hashKey = CF::config('app.key');

        // It's actually Ok if the "children" tracking is tampered with.
        // Also, this way JavaScript can modify children as it needs to for
        // dom-diffing purposes.
        $memoSansChildren = array_diff_key($memo, array_flip(['children']));

        $stringForHashing = ''
                . json_encode($fingerprint)
                . json_encode($memoSansChildren);

        return hash_hmac('sha256', $stringForHashing, $hashKey);
    }

    public function check($checksum, $fingerprint, $memo) {
        return hash_equals($this->generate($fingerprint, $memo), $checksum);
    }
}
