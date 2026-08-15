<?php

use PHPUnit\Framework\TestCase;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * CAuth_Exception_AuthorizationException sits in $internalDontReport, but
 * CApi_OAuth_Bridge_UserRepository rewraps it as an OAuthServerException before
 * it ever reaches the handler, so every wrong password was logged as ERROR.
 * Only 4xx is treated as a client-side failure; serverError() still reports.
 */
class OAuthFailureReportingTest extends TestCase {
    /**
     * @return CException_ExceptionHandler
     */
    private function handler() {
        return new CException_ExceptionHandler();
    }

    /**
     * @return void
     */
    public function testAWrongPasswordIsNotReported() {
        $e = new OAuthServerException('Email atau kata sandi salah!', 9, 'access_denied', 401, 'Email atau kata sandi salah!');

        $this->assertFalse($this->handler()->shouldReport($e));
    }

    /**
     * @return void
     */
    public function testAnInvalidRefreshTokenIsNotReported() {
        $e = OAuthServerException::invalidRefreshToken('The refresh token is invalid.');

        $this->assertFalse($this->handler()->shouldReport($e));
    }

    /**
     * @return void
     */
    public function testInvalidCredentialsAreNotReported() {
        $this->assertFalse($this->handler()->shouldReport(OAuthServerException::invalidCredentials()));
    }

    /**
     * The half that must keep working: a genuine fault behind the OAuth
     * endpoint is still a fault, and staying silent about it would hide it.
     *
     * @return void
     */
    public function testAnOAuthServerErrorIsStillReported() {
        $e = OAuthServerException::serverError('database gone');

        $this->assertSame(500, $e->getHttpStatusCode());
        $this->assertTrue($this->handler()->shouldReport($e));
    }

    /**
     * @return void
     */
    public function testUnrelatedExceptionsAreUnaffected() {
        $handler = $this->handler();

        $this->assertTrue($handler->shouldReport(new RuntimeException('boom')));
        $this->assertFalse($handler->shouldReport(new CAuth_Exception_AuthorizationException('nope')));
    }
}
