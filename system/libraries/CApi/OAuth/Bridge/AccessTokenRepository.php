<?php

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class CApi_OAuth_Bridge_AccessTokenRepository implements AccessTokenRepositoryInterface {
    use CApi_OAuth_Bridge_Trait_FormatScopesForStorageTrait;

    /**
     * The token repository instance.
     *
     * @var \CApi_OAuth_TokenRepository
     */
    protected $tokenRepository;

    /**
     * The event dispatcher instance.
     *
     * @var \CEvent_DispatcherInterface
     */
    protected $events;

    /**
     * Create a new repository instance.
     *
     * @param \CApi_OAuth_TokenRepository $tokenRepository
     *
     * @return void
     */
    public function __construct(CApi_OAuth_TokenRepository $tokenRepository) {
        $this->events = CEvent::dispatcher();
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @inheritdoc
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null) {
        return new CApi_OAuth_Bridge_AccessToken($userIdentifier, $scopes, $clientEntity);
    }

    /**
     * @inheritdoc
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity) {
        $tokenData = [
            'token' => $accessTokenEntity->getIdentifier(),
            'user_id' => $accessTokenEntity->getUserIdentifier(),
            'user_type' => $this->tokenRepository->oauth()->getUserModelFromProvider(),
            'oauth_client_id' => $accessTokenEntity->getClient()->getIdentifier(),
            'scopes' => $this->scopesToArray($accessTokenEntity->getScopes()),
            'revoked' => false,
            'created' => c::now(),
            'createdby' => c::base()->username(),
            'updated' => c::now(),
            'updatedby' => c::base()->username(),
            'expires_at' => $accessTokenEntity->getExpiryDateTime(),
        ];
        $tokenModel = $this->tokenRepository->create($tokenData);
        $this->events->dispatch(new CApi_OAuth_Event_AccessTokenCreated(
            $accessTokenEntity->getIdentifier(),
            $accessTokenEntity->getUserIdentifier(),
            $accessTokenEntity->getClient()->getIdentifier(),
            $tokenModel,
        ));
    }

    /**
     * @inheritdoc
     */
    public function revokeAccessToken($tokenId) {
        $this->tokenRepository->revokeAccessToken($tokenId);
    }

    /**
     * @inheritdoc
     */
    public function isAccessTokenRevoked($tokenId) {
        return $this->tokenRepository->isAccessTokenRevoked($tokenId);
    }
}
