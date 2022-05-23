<?php

use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/**
 * @deprecated 6.0.1
 * @codeCoverageIgnore
 */
final class CVendor_Firebase_Auth_CreateSessionCookie_ApiRequest implements RequestInterface {
    use CVendor_Firebase_Trait_WrappedPsr7RequestTrait;

    public function __construct(CVendor_Firebase_Auth_CreateSessionCookie $action) {
        $uri = Utils::uriFor('https://www.googleapis.com/identitytoolkit/v3/relyingparty/createSessionCookie');

        $data = [
            'idToken' => $action->idToken(),
            'validDuration' => $action->ttlInSeconds(),
        ];

        $body = Utils::streamFor(CVendor_Firebase_Util_JSON::encode($data, JSON_FORCE_OBJECT));

        $headers = \array_filter([
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Length' => (string) $body->getSize(),
        ]);

        $this->wrappedRequest = new Request('POST', $uri, $headers, $body);
    }
}
