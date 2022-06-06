<?php

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class CApi_OAuth_Bridge_RefreshTokenRepository implements RefreshTokenRepositoryInterface {
    /**
     * The refresh token repository instance.
     *
     * @var \CApi_OAuth_RefreshTokenRepository
     */
    protected $refreshTokenRepository;

    /**
     * The event dispatcher instance.
     *
     * @var \CEvent_DispatcherInterface
     */
    protected $events;

    /**
     * Create a new repository instance.
     *
     * @param \Laravel\Passport\RefreshTokenRepository $refreshTokenRepository
     *
     * @return void
     */
    public function __construct(CApi_OAuth_RefreshTokenRepository $refreshTokenRepository) {
        $this->events = CEvent::dispatcher();
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    /**
     * @inheritdoc
     */
    public function getNewRefreshToken() {
        return new CApi_OAuth_Bridge_RefreshToken();
    }

    /**
     * @inheritdoc
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity) {
        $this->refreshTokenRepository->create([
            'id' => $id = $refreshTokenEntity->getIdentifier(),
            'access_token_id' => $accessTokenId = $refreshTokenEntity->getAccessToken()->getIdentifier(),
            'revoked' => false,
            'expires_at' => $refreshTokenEntity->getExpiryDateTime(),
        ]);

        $this->events->dispatch(new CApi_OAuth_Event_RefreshTokenCreated($id, $accessTokenId));
    }

    /**
     * @inheritdoc
     */
    public function revokeRefreshToken($tokenId) {
        $this->refreshTokenRepository->revokeRefreshToken($tokenId);
    }

    /**
     * @inheritdoc
     */
    public function isRefreshTokenRevoked($tokenId) {
        return $this->refreshTokenRepository->isRefreshTokenRevoked($tokenId);
    }
}
