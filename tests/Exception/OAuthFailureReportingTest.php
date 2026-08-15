<?php

use PHPUnit\Framework\TestCase;
use League\OAuth2\Server\Exception\OAuthServerException as LeagueException;

/**
 * CAuth_Exception_AuthorizationException sits in $internalDontReport, but by the
 * time the handler sees it the exception has been rewrapped twice: once into a
 * LeagueException by CApi_OAuth_Bridge_UserRepository, then into a
 * CApi_OAuth_Exception_OAuthServerException by
 * CApi_OAuth_Trait_HandleOAuthErrorTrait::withErrorHandling(). That last class
 * extends plain Exception, so every wrong password was logged as ERROR.
 */
/**
 * Membuka withErrorHandling() tanpa menjalankan peladen OAuth sungguhan.
 */
class OAuthFailureReportingTestRunner {
    use CApi_OAuth_Trait_HandleOAuthErrorTrait;

    /**
     * @param \Closure $callback
     *
     * @return mixed
     */
    public function run($callback) {
        return $this->withErrorHandling($callback);
    }
}

class OAuthFailureReportingTest extends TestCase {
    /**
     * @param string $message
     * @param int    $status
     *
     * @return CApi_OAuth_Exception_OAuthServerException
     */
    private function thrownException($message, $status) {
        return new CApi_OAuth_Exception_OAuthServerException(
            new LeagueException($message, 9, 'access_denied', $status, $message),
            new CHTTP_Response('', $status)
        );
    }

    /**
     * @return CException_ExceptionHandler
     */
    private function handler() {
        return new CException_ExceptionHandler();
    }

    /**
     * The class that actually reaches the handler is the wrapper, not the
     * LeagueException - guarding that here because it is what the fix hinges on.
     *
     * @return void
     */
    public function testTheWrapperIsNotALeagueException() {
        $e = $this->thrownException('Email atau kata sandi salah!', 401);

        $this->assertInstanceOf('Exception', $e);
        $this->assertNotInstanceOf(LeagueException::class, $e);
    }

    /**
     * @return void
     */
    public function testAWrongPasswordIsHandledAndNotLogged() {
        $e = $this->thrownException('Email atau kata sandi salah!', 401);

        $this->assertTrue($e->report());
    }

    /**
     * @return void
     */
    public function testAnInvalidRefreshTokenIsHandledAndNotLogged() {
        $e = $this->thrownException('The refresh token is invalid.', 401);

        $this->assertTrue($e->report());
    }

    /**
     * @return void
     */
    public function testABadRequestIsAlsoHandled() {
        $this->assertTrue($this->thrownException('Invalid grant', 400)->report());
    }

    /**
     * The half that must keep working: a genuine fault behind the OAuth endpoint
     * is still a fault, and staying silent about it would hide it.
     *
     * @return void
     */
    public function testAServerErrorIsStillReported() {
        $e = $this->thrownException('database gone', 500);

        $this->assertFalse($e->report());
    }

    /**
     * The whole chain, not just the wrapper: CApi_OAuth_Bridge_UserRepository
     * still throws a plain LeagueException, and it is withErrorHandling() that
     * turns it into the wrapper. The status set at the throw site is what the
     * wrapper later reads back through generateHttpResponse().
     *
     * @return void
     */
    public function testTheStatusSurvivesTheRewrapDoneByWithErrorHandling() {
        $runner = new OAuthFailureReportingTestRunner();

        try {
            $runner->run(function () {
                throw new LeagueException('Email atau kata sandi salah!', 9, 'access_denied', 401, 'Email atau kata sandi salah!');
            });
            $this->fail('withErrorHandling() seharusnya melempar ulang');
        } catch (CApi_OAuth_Exception_OAuthServerException $e) {
            $this->assertInstanceOf(LeagueException::class, $e->getPrevious());
            $this->assertSame(401, $e->statusCode());
            $this->assertTrue($e->report());
        }
    }

    /**
     * @return void
     */
    public function testAServerErrorSurvivesTheRewrapAsReportable() {
        $runner = new OAuthFailureReportingTestRunner();

        try {
            $runner->run(function () {
                throw LeagueException::serverError('database gone');
            });
            $this->fail('withErrorHandling() seharusnya melempar ulang');
        } catch (CApi_OAuth_Exception_OAuthServerException $e) {
            $this->assertSame(500, $e->statusCode());
            $this->assertFalse($e->report());
        }
    }

    /**
     * The handler consults report() before logging, so a false answer must still
     * leave the exception reportable.
     *
     * @return void
     */
    public function testTheHandlerStillConsidersTheWrapperReportable() {
        $handler = $this->handler();

        $this->assertTrue($handler->shouldReport($this->thrownException('database gone', 500)));
        $this->assertTrue($handler->shouldReport(new RuntimeException('boom')));
        $this->assertFalse($handler->shouldReport(new CAuth_Exception_AuthorizationException('nope')));
    }
}
