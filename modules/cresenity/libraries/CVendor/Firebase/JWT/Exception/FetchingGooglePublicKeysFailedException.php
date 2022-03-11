<?php

final class CVendor_Firebase_JWT_Exception_FetchingGooglePublicKeysFailedException extends RuntimeException {
    /**
     * @param string         $reason
     * @param null|int       $code
     * @param null|Throwable $previous
     *
     * @return self
     */
    public static function because($reason, $code = null, $previous = null) {
        $code = $code ?: 0;

        return new self($reason, $code, $previous);
    }
}
