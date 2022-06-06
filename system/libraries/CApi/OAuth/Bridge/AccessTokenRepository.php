<?php

use Illuminate\Contracts\Events\Dispatcher;
use Laravel\Passport\Events\AccessTokenCreated;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface {
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
        return new AccessToken($userIdentifier, $scopes, $clientEntity);
    }

    /**
     * @inheritdoc
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity) {
        $this->tokenRepository->create([
            'id' => $accessTokenEntity->getIdentifier(),
            'user_id' => $accessTokenEntity->getUserIdentifier(),
            'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
            'scopes' => $this->scopesToArray($accessTokenEntity->getScopes()),
            'revoked' => false,
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),
            'expires_at' => $accessTokenEntity->getExpiryDateTime(),
        ]);

        $this->events->dispatch(new AccessTokenCreated(
            $accessTokenEntity->getIdentifier(),
            $accessTokenEntity->getUserIdentifier(),
            $accessTokenEntity->getClient()->getIdentifier()
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
