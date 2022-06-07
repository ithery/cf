<?php

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class CApi_OAuth_Bridge_AuthCodeRepository implements AuthCodeRepositoryInterface {
    use CApi_OAuth_Bridge_Trait_FormatScopesForStorageTrait;

    /**
     * @var CApi_OAuth
     */
    protected $oauth;

    public function __construct(CApi_OAuth $oauth) {
        $this->oauth = $oauth;
    }

    /**
     * @inheritdoc
     */
    public function getNewAuthCode() {
        return new CApi_OAuth_Bridge_AuthCode();
    }

    /**
     * @inheritdoc
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity) {
        $userType = $this->oauth->getUserModelFromProvider();
        $attributes = [
            'code' => $authCodeEntity->getIdentifier(),
            'user_id' => $authCodeEntity->getUserIdentifier(),
            'user_type' => $userType,
            'oauth_client_id' => $authCodeEntity->getClient()->getIdentifier(),
            'scopes' => $this->formatScopesForStorage($authCodeEntity->getScopes()),
            'revoked' => false,
            'expires_at' => $authCodeEntity->getExpiryDateTime(),
            'createdby' => c::base()->username(),
            'updatedby' => c::base()->username(),
        ];

        $this->oauth->authCode()->forceFill($attributes)->save();
    }

    /**
     * @inheritdoc
     */
    public function revokeAuthCode($codeId) {
        $this->oauth->authCode()->where('code', $codeId)->update(['revoked' => true]);
    }

    /**
     * @inheritdoc
     */
    public function isAuthCodeRevoked($codeId) {
        return $this->oauth->authCode()->where('code', $codeId)->where('revoked', 1)->exists();
    }
}
