<?php

use PHPUnit\Framework\TestCase;

/**
 * Tests for CHTTP_RedirectResponse (system/libraries/CHTTP/RedirectResponse.php).
 *
 * These exercise the session-flash based helpers (with/withInput/withErrors), which
 * require a real CSession_Store. phpunit.xml sets SESSION_DRIVER=array, so c::session()
 * lazily boots an in-memory session store the first time it's touched. Because that
 * store is cached as a process-wide singleton (CBase::$session), we flush it before
 * each test so flashed data doesn't leak between tests.
 */
class HttpRedirectResponseTest extends TestCase {
    protected function setUp(): void {
        parent::setUp();

        if (CSession::sessionConfigured()) {
            c::session()->flush();
        }
    }

    public function testWithFlashesSingleKeyToSession() {
        $response = new CHTTP_RedirectResponse('/home');
        $result = $response->with('status', 'saved');

        $this->assertSame($response, $result);
        $this->assertSame('saved', c::session()->get('status'));
    }

    public function testWithFlashesArrayToSession() {
        $response = new CHTTP_RedirectResponse('/home');
        $response->with(['one' => 1, 'two' => 2]);

        $this->assertSame(1, c::session()->get('one'));
        $this->assertSame(2, c::session()->get('two'));
    }

    public function testWithCookiesAddsCookiesToHeaders() {
        $cookie = new Symfony\Component\HttpFoundation\Cookie('name', 'value');

        $response = new CHTTP_RedirectResponse('/home');
        $result = $response->withCookies([$cookie]);

        $this->assertSame($response, $result);
        $this->assertSame('name', $response->headers->getCookies()[0]->getName());
    }

    public function testWithInputFlashesGivenArray() {
        $response = new CHTTP_RedirectResponse('/home');
        $response->withInput(['field' => 'value']);

        $this->assertSame(['field' => 'value'], c::session()->getOldInput());
    }

    public function testWithInputRemovesUploadedFiles() {
        $tmp = tempnam(sys_get_temp_dir(), 'up');
        file_put_contents($tmp, 'contents');
        $file = new Symfony\Component\HttpFoundation\File\UploadedFile($tmp, 'x.txt', 'text/plain', null, null, true);

        $response = new CHTTP_RedirectResponse('/home');
        $response->withInput([
            'name' => 'John',
            'avatar' => $file,
            'nested' => ['inner_file' => $file, 'keep' => 'yes'],
        ]);

        $old = c::session()->getOldInput();

        $this->assertSame('John', $old['name']);
        $this->assertArrayNotHasKey('avatar', $old);
        $this->assertArrayNotHasKey('inner_file', $old['nested']);
        $this->assertSame('yes', $old['nested']['keep']);

        @unlink($tmp);
    }

    public function testOnlyInputFlashesSubsetOfRequestInput() {
        $request = CHTTP_Request::create('/submit', 'POST', ['a' => '1', 'b' => '2', 'c' => '3']);

        $response = new CHTTP_RedirectResponse('/home');
        $response->setRequest($request);
        $response->onlyInput('a', 'c');

        $this->assertSame(['a' => '1', 'c' => '3'], c::session()->getOldInput());
    }

    public function testExceptInputFlashesRemainderOfRequestInput() {
        $request = CHTTP_Request::create('/submit', 'POST', ['a' => '1', 'b' => '2', 'c' => '3']);

        $response = new CHTTP_RedirectResponse('/home');
        $response->setRequest($request);
        $response->exceptInput('b');

        $this->assertSame(['a' => '1', 'c' => '3'], c::session()->getOldInput());
    }

    public function testWithErrorsFromArray() {
        $response = new CHTTP_RedirectResponse('/home');
        $result = $response->withErrors(['name' => 'The name field is required.']);

        $this->assertSame($response, $result);

        /** @var CBase_ViewErrorBag $errors */
        $errors = c::session()->get('errors');

        $this->assertInstanceOf(CBase_ViewErrorBag::class, $errors);
        $this->assertTrue($errors->getBag('default')->has('name'));
        $this->assertSame(
            'The name field is required.',
            $errors->getBag('default')->first('name')
        );
    }

    public function testWithErrorsFromMessageProviderUnderNamedKey() {
        $bag = new CBase_MessageBag(['email' => 'Invalid email.']);

        $response = new CHTTP_RedirectResponse('/home');
        $response->withErrors($bag, 'loginForm');

        /** @var CBase_ViewErrorBag $errors */
        $errors = c::session()->get('errors');

        $this->assertTrue($errors->hasBag('loginForm'));
        $this->assertSame('Invalid email.', $errors->getBag('loginForm')->first('email'));
        $this->assertFalse($errors->hasBag('default'));
    }

    public function testWithErrorsPreservesExistingErrorBags() {
        $response = new CHTTP_RedirectResponse('/home');
        $response->withErrors(['a' => 'first error'], 'first');
        $response->withErrors(['b' => 'second error'], 'second');

        /** @var CBase_ViewErrorBag $errors */
        $errors = c::session()->get('errors');

        $this->assertTrue($errors->hasBag('first'));
        $this->assertTrue($errors->hasBag('second'));
    }

    public function testWithFragmentAppendsFragmentToTargetUrl() {
        $response = new CHTTP_RedirectResponse('/home');
        $result = $response->withFragment('section-1');

        $this->assertSame($response, $result);
        $this->assertSame('/home#section-1', $response->getTargetUrl());
    }

    public function testWithFragmentReplacesExistingFragment() {
        $response = new CHTTP_RedirectResponse('/home#old');
        $response->withFragment('new');

        $this->assertSame('/home#new', $response->getTargetUrl());
    }

    public function testWithoutFragmentRemovesFragment() {
        $response = new CHTTP_RedirectResponse('/home#section');
        $result = $response->withoutFragment();

        $this->assertSame($response, $result);
        $this->assertSame('/home', $response->getTargetUrl());
    }

    public function testGetOriginalContentReturnsNull() {
        $response = new CHTTP_RedirectResponse('/home');

        $this->assertNull($response->getOriginalContent());
    }

    public function testGetAndSetRequest() {
        $response = new CHTTP_RedirectResponse('/home');
        $this->assertNull($response->getRequest());

        $request = CHTTP_Request::create('/home');
        $response->setRequest($request);

        $this->assertSame($request, $response->getRequest());
    }

    public function testGetTargetUrlAndSetTargetUrl() {
        $response = new CHTTP_RedirectResponse('/original');
        $this->assertSame('/original', $response->getTargetUrl());

        $response->setTargetUrl('/changed');
        $this->assertSame('/changed', $response->getTargetUrl());
    }

    public function testMagicCallFlashesDynamicWithKey() {
        $response = new CHTTP_RedirectResponse('/home');
        $response->withStatus('ok');

        $this->assertSame('ok', c::session()->get('status'));
    }

    public function testMagicCallThrowsForUnknownMethod() {
        $response = new CHTTP_RedirectResponse('/home');

        $this->expectException(BadMethodCallException::class);

        $response->doesNotExist();
    }

    public function testSessionHelperReturnsSessionStore() {
        $response = new CHTTP_RedirectResponse('/home');

        $this->assertInstanceOf(CSession_Store::class, $response->session());
    }
}
