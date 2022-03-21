<?php

use GuzzleHttp\RequestOptions;

class CSocialLogin_OAuth2_Provider_AtlassianProvider extends CSocialLogin_OAuth2_AbstractProvider implements CSocialLogin_OAuth2_ProviderInterface {
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'ATLASSIAN';

    /**
     * @inheritdoc
     */
    protected $parameters = [
        'prompt' => 'consent',
        'audience' => 'api.atlassian.com',
    ];

    /**
     * @inheritdoc
     */
    protected $scopes = ['read:me'];

    /**
     * @inheritdoc
     */
    protected function getAuthUrl($state) {
        return $this->buildAuthUrlFromBase('https://auth.atlassian.com/authorize', $state);
    }

    /**
     * @inheritdoc
     */
    protected function getTokenUrl() {
        return 'https://auth.atlassian.com/oauth/token';
    }

    /**
     * @inheritdoc
     */
    protected function getUserByToken($token) {
        $response = $this->getHttpClient()->get('https://api.atlassian.com/me', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * @inheritdoc
     */
    protected function mapUserToObject(array $user) {
        return (new CSocialLogin_OAuth2_User())->setRaw($user)->map([
            'id' => $user['account_id'],
            'nickname' => $user['email'],
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar' => $user['picture'],
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function getTokenFields($code) {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
