<?php

interface CHTTP_Resources_PotentiallyMissing {
    /**
     * Determine if the object should be considered "missing".
     *
     * @return bool
     */
    public function isMissing();
}
