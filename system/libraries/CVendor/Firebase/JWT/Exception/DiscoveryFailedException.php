<?php

/**
 * @codeCoverageIgnore
 */
final class CVendor_Firebase_JWT_Exception_DiscoveryFailedException extends LogicException {
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

    public static function noJWTLibraryFound(): self {
        $message = <<<'MESSAGE'
Unable to create token handlers. Please install one of the following JWT libraries:
- firebase/php-jwt ^5.0 (https://github.com/firebase/php-jwt)
- lcobucci/jwt ^3.2 (https://github.com/lcobucci/jwt)
or implement handlers that implement
Kreait\Firebase\JWT\Action\CreateCustomToken\Handler
and
Kreait\Firebase\JWT\Action\VerifyIdToken\Handler
MESSAGE;

        return self::because($message);
    }

    public static function noHttpLibraryFound(): self {
        $message = <<<'MESSAGE'
Unable to find a HTTP transport to fetch public keys from Google. Please set
`allow_url_fopen = On` in your php.ini or use one of the
following supported HTTP libraries:
- guzzlehttp/guzzle ^6.2.1 (https://github.com/guzzle/guzzle)
or implement your own handler that implements
Kreait\Firebase\JWT\Action\FetchGooglePublicKeys\Handler
MESSAGE;

        return self::because($message);
    }
}
