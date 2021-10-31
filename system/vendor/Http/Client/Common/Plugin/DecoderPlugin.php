<?php

declare(strict_types=1);

namespace Http\Client\Common\Plugin;

use Http\Client\Common\Plugin;
use Http\Client\Exception\TransferException;
use Http\Message\Cookie;
use Http\Message\CookieJar;
use Http\Message\CookieUtil;
use Http\Message\Exception\UnexpectedValueException;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Handle request cookies.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class CookiePlugin implements Plugin
{
    /**
     * Cookie storage.
     *
     * @var CookieJar
     */
    private $cookieJar;

    public function __construct(CookieJar $cookieJar)
    {
        $this->cookieJar = $cookieJar;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $cookies = [];
        foreach ($this->cookieJar->getCookies() as $cookie) {
            if ($cookie->isExpired()) {
                continue;
            }

            if (!$cookie->matchDomain($request->getUri()->getHost())) {
                continue;
            }

            if (!$cookie->matchPath($request->getUri()->getPath())) {
                continue;
            }

            if ($cookie->isSecure() && ('https' !== $request->getUri()->getScheme())) {
                continue;
            }

            $cookies[] = sprintf('%s=%s', $cookie->getName(), $cookie->getValue());
        }

        if (!empty($cookies)) {
            $request = $request->withAddedHeader('Cookie', implode('; ', array_unique($cookies)));
        }

        return $next($request)->then(function (ResponseInterface $response) use ($request) {
            if ($response->hasHeader('Set-Cookie')) {
                $setCookies = $response->getHeader('Set-Cookie');

                foreach ($setCookies as $setCookie) {
                    $cookie = $this->createCookie($request, $setCookie);

                    // Cookie invalid do not use it
                    if (null === $cookie) {
                        continue;
                    }

                    // Restrict setting cookie from another domain
                    if (!preg_match("/\.{$cookie->getDomain()}$/", '.'.$request->getUri()->getHost())) {
                        continue;
                    }

                    $this->cookieJar->addCookie($cookie);
                }
            }

            return $response;
        });
    }

    /**
     * Creates a cookie from a string.
     *
     * @throws TransferException
     */
    private function createCookie(RequestInterface $request, string $setCookieHeader): ?Cookie
    {
        $parts = array_map('trim', explode(';', $setCookieHeader));

        if (empty($parts) || !strpos($parts[0], '=')) {
            return null;
        }

        list($name, $cookieValue) = $this->createValueKey(array_shift($parts));

        $maxAge = null;
        $expires = null;
        $domain = $request->getUri()->getHost();
        $path = $request->getUri()->getPath();
        $secure = false;
        $httpOnly = false;

        // Add the cookie pieces into the parsed data array
        foreach ($parts as $part) {
            list($key, $value) = $this->createValueKey($part);

    = simplexml_load_string($stream->getContents());
        libxml_use_internal_errors($previousValue);

        return false !== $isXml;
    }
}
