<?php

use GuzzleHttp\RequestOptions;

class CSocialLogin_OAuth2_Provider_DropboxProvider extends CSocialLogin_OAuth2_AbstractProvider implements CSocialLogin_OAuth2_ProviderInterface {
    public const IDENTIFIER = 'DROPBOX';

    protected $scopeSeparator = ' ';

    protected $scopes = [
        'account_info.read',
    ];

    protected function getAuthUrl($state): string {
        return $this->buildAuthUrlFromBase('https://www.dropbox.com/oauth2/authorize', $state);
    }

    protected function getTokenUrl(): string {
        return 'https://api.dropboxapi.com/oauth2/token';
    }

    /**
     * @inheritdoc
     */
    protected function getUserByToken($token) {
        $response = $this->getHttpClient()->post(
            'https://api.dropboxapi.com/2/users/get_current_account',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]
        );

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * @inheritdoc
     */
    protected function mapUserToObject(array $user) {
        return (new CSocialLogin_OAuth2_User())->setRaw($user)->map([
            'id' => $user['account_id'],
            'nickname' => null,
            'name' => $user['name']['display_name'],
            'email' => $user['email'],
            'avatar' => carr::get($user, 'profile_photo_url'),
        ]);
    }
}
