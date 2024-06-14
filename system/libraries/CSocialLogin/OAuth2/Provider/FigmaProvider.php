<?php

defined('SYSPATH') or die('No direct access allowed.');

use GuzzleHttp\RequestOptions;

class CSocialLogin_OAuth2_Provider_FigmaProvider extends CSocialLogin_OAuth2_AbstractProvider implements CSocialLogin_OAuth2_ProviderInterface {
    public const IDENTIFIER = 'FIGMA';

    /**
     * @inheritdoc
     */
    protected $scopes = ['file_read'];

    /**
     * @inheritdoc
     */
    protected function getAuthUrl($state) {
        return $this->buildAuthUrlFromBase('https://www.figma.com/oauth', $state);
    }

    /**
     * @inheritdoc
     */
    protected function getTokenUrl() {
        return 'https://www.figma.com/api/oauth/token';
    }

    /**
     * @inheritdoc
     */
    protected function getUserByToken($token) {
        $response = $this->getHttpClient()->get('https://api.figma.com/v1/me', [
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
            'id' => $user['id'],
            'email' => $user['email'],
            'nickname' => $user['handle'],
            'name' => $user['handle'],
            'avatar' => $user['img_url'],
        ]);
    }
}
