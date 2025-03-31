<?php

class CHTTP_Resources_MissingValue implements CHTTP_Resources_PotentiallyMissing {
    /**
     * Determine if the object should be considered "missing".
     *
     * @return bool
     */
    public function isMissing() {
        return true;
    }
}
