<?php

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class CApi_OAuth_Bridge_AuthCodeRepository implements AuthCodeRepositoryInterface {
    use CApi_OAuth_Bridge_Trait_FormatScopesForStorageTrait;

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
        $attributes = [
            'id' => $authCodeEntity->getIdentifier(),
            'user_id' => $authCodeEntity->getUserIdentifier(),
            'client_id' => $authCodeEntity->getClient()->getIdentifier(),
            'scopes' => $this->formatScopesForStorage($authCodeEntity->getScopes()),
            'revoked' => false,
            'expires_at' => $authCodeEntity->getExpiryDateTime(),
        ];

        CApi::oauth()->authCode()->forceFill($attributes)->save();
    }

    /**
     * @inheritdoc
     */
    public function revokeAuthCode($codeId) {
        CApi::oauth()->authCode()->where('id', $codeId)->update(['revoked' => true]);
    }

    /**
     * @inheritdoc
     */
    public function isAuthCodeRevoked($codeId) {
        return CApi::oauth()->authCode()->where('id', $codeId)->where('revoked', 1)->exists();
    }
}
