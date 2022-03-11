<?php

final class CVendor_Firebase_JWT_Exception_IdTokenVerificationFailedException extends RuntimeException {
    /**
     * @param array<int|string, string> $reasons
     * @param string                    $token
     *
     * @return self
     */
    public static function withTokenAndReasons($token, array $reasons) {
        if (\mb_strlen($token) > 18) {
            $token = \mb_substr($token, 0, 15) . '...';
        }

        $summary = \implode(\PHP_EOL . '- ', $reasons);

        $message = "The value '{$token}' is not a verified ID token:" . \PHP_EOL . '- ' . $summary . \PHP_EOL;

        return new self($message);
    }
}
