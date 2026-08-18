<?php

use League\OAuth2\Server\RequestEvent;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;

class CApi_OAuth_Bridge_SocialGrant extends PasswordGrant {
    /**
     * @param ServerRequestInterface $request
     * @param ClientEntityInterface  $client
     *
     * @throws OAuthServerException
     *
     * @return UserEntityInterface
     */
    protected function validateUser(ServerRequestInterface $request, ClientEntityInterface $client) {
        $provider = $this->getRequestParameter('provider', $request);

        if (!\is_string($provider)) {
            throw OAuthServerException::invalidRequest('provider');
        }

        $accessToken = $this->getRequestParameter('access_token', $request);

        if (!\is_string($accessToken)) {
            throw OAuthServerException::invalidRequest('access_token');
        }

        $user = $this->userRepository->getUserEntityBySocialLogin(
            $provider,
            $accessToken,
            $this->getIdentifier(),
            $client
        );

        if ($user instanceof UserEntityInterface === false) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier() {
        return 'social';
    }
}
