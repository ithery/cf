<?php

use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/**
 * @deprecated 6.0.1
 * @codeCoverageIgnore
 */
final class CVendor_Firebase_Auth_SendActionLink_ApiRequest implements RequestInterface {
    use CVendor_Firebase_Trait_WrappedPsr7RequestTrait;

    public function __construct(CVendor_Firebase_Auth_SendActionLink $action) {
        $uri = Utils::uriFor('https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode');

        $data = \array_filter([
            'requestType' => $action->type(),
            'email' => $action->email(),
            'tenantId' => $action->tenantId(),
        ]) + $action->settings()->toArray();

        if ($idTokenString = $action->idTokenString()) {
            $data['idToken'] = $idTokenString;
        }

        $body = Utils::streamFor(CVendor_Firebase_Util_JSON::encode($data, JSON_FORCE_OBJECT));

        $headers = \array_filter([
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Length' => (string) $body->getSize(),
            'X-Firebase-Locale' => $action->locale(),
        ]);

        $this->wrappedRequest = new Request('POST', $uri, $headers, $body);
    }
}
