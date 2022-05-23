<?php

use GuzzleHttp\RequestOptions;

class CSocialLogin_OAuth2_Provider_DiscordProvider extends CSocialLogin_OAuth2_AbstractProvider implements CSocialLogin_OAuth2_ProviderInterface {
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'DISCORD';

    /**
     * @inheritdoc
     */
    protected $scopes = [
        'identify',
        'email',
    ];

    /**
     * @inheritdoc
     */
    protected $scopeSeparator = ' ';

    /**
     * @inheritdoc
     */
    protected function getAuthUrl($state) {
        return $this->buildAuthUrlFromBase(
            'https://discord.com/api/oauth2/authorize',
            $state
        );
    }

    /**
     * @inheritdoc
     */
    protected function getTokenUrl() {
        return 'https://discord.com/api/oauth2/token';
    }

    /**
     * @inheritdoc
     */
    protected function getUserByToken($token) {
        $response = $this->getHttpClient()->get(
            'https://discord.com/api/users/@me',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]
        );

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * @param array $user
     *
     * @return null|string
     *
     * @see https://discord.com/developers/docs/reference#image-formatting-cdn-endpoints
     */
    protected function formatAvatar(array $user) {
        if (empty($user['avatar'])) {
            return null;
        }

        $isGif = preg_match('/a_.+/m', $user['avatar']) === 1;
        $extension = $this->getConfig('allow_gif_avatars', true) && $isGif ? 'gif'
            : $this->getConfig('avatar_default_extension', 'jpg');

        return sprintf('https://cdn.discordapp.com/avatars/%s/%s.%s', $user['id'], $user['avatar'], $extension);
    }

    /**
     * @inheritdoc
     */
    protected function mapUserToObject(array $user) {
        return (new CSocialLogin_OAuth2_User())->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => sprintf('%s#%s', $user['username'], $user['discriminator']),
            'name' => $user['username'],
            'email' => $user['email'] ?? null,
            'avatar' => $this->formatAvatar($user),
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

    /**
     * @inheritdoc
     */
    public static function additionalConfigKeys() {
        return ['allow_gif_avatars', 'avatar_default_extension'];
    }
}
