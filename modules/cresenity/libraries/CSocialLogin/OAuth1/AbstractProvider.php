<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 16, 2019, 4:24:44 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Credentials\TokenCredentials;

abstract class CSocialLogin_OAuth1_AbstractProvider extends CSocialLogin_AbstractProvider {

    /**
     * The OAuth server implementation.
     *
     * @var \League\OAuth1\Client\Server\Server
     */
    protected $server;

    /**
     * A hash representing the last requested user.
     *
     * @var string
     */
    protected $userHash;

    /**
     * Create a new provider instance.
     *
     * @param  \League\OAuth1\Client\Server\Server  $server
     * @return void
     */
    public function __construct(Server $server) {
        $this->server = $server;
    }

    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return void
     */
    public function redirect() {
        $this->session()->set('oauth.temp', $temp = $this->server->getTemporaryCredentials());
        curl::redirect($this->server->getAuthorizationUrl($temp));
    }

    /**
     * Get the User instance for the authenticated user.
     *
     * @throws \InvalidArgumentException
     * @return \CSocialLogin_OAuth1_User
     */
    public function user() {
        if (!$this->hasNecessaryVerifier()) {
            throw new InvalidArgumentException('Invalid request. Missing OAuth verifier.');
        }
        $token = $this->getToken();
        
        $user = $this->server->getUserDetails(
                $token, $this->shouldBypassCache($token->getIdentifier(), $token->getSecret())
        );
        $instance = (new CSocialLogin_OAuth1_User)->setRaw($user->extra)
                ->setToken($token->getIdentifier(), $token->getSecret());
        return $instance->map([
                    'id' => $user->uid,
                    'nickname' => $user->nickname,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->imageUrl,
        ]);
    }

    /**
     * Get a Social User instance from a known access token and secret.
     *
     * @param  string  $token
     * @param  string  $secret
     * @return \CSocialLogin_OAuth1_User
     */
    public function userFromTokenAndSecret($token, $secret) {
        $tokenCredentials = new TokenCredentials();
        $tokenCredentials->setIdentifier($token);
        $tokenCredentials->setSecret($secret);
        $user = $this->server->getUserDetails(
                $tokenCredentials, $this->shouldBypassCache($token, $secret)
        );
        $instance = (new CSocialLogin_OAuth1_User)->setRaw($user->extra)
                ->setToken($tokenCredentials->getIdentifier(), $tokenCredentials->getSecret());
        return $instance->map([
                    'id' => $user->uid,
                    'nickname' => $user->nickname,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->imageUrl,
        ]);
    }

    /**
     * Get the token credentials for the request.
     *
     * @return \League\OAuth1\Client\Credentials\TokenCredentials
     */
    protected function getToken() {
        $temp = $this->session()->get('oauth.temp');
        return $this->server->getTokenCredentials(
                        $temp, $this->input('oauth_token'), $this->input('oauth_verifier')
        );
    }

    /**
     * Determine if the request has the necessary OAuth verifier.
     *
     * @return bool
     */
    protected function hasNecessaryVerifier() {
        return $this->hasRequest('oauth_token') && $this->hasRequest('oauth_verifier');
    }

    /**
     * Determine if the user information cache should be bypassed.
     *
     * @param  string  $token
     * @param  string  $secret
     * @return bool
     */
    protected function shouldBypassCache($token, $secret) {
        $newHash = sha1($token . '_' . $secret);
        if (!empty($this->userHash) && $newHash !== $this->userHash) {
            $this->userHash = $newHash;
            return true;
        }
        $this->userHash = $this->userHash ?: $newHash;
        return false;
    }

}
