<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 16, 2019, 4:30:48 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CSocialLogin_OAuth1_User extends CSocialLogin_AbstractUser {

    /**
     * The user's access token.
     *
     * @var string
     */
    public $token;

    /**
     * The user's access token secret.
     *
     * @var string
     */
    public $tokenSecret;

    /**
     * Set the token on the user.
     *
     * @param  string  $token
     * @param  string  $tokenSecret
     * @return $this
     */
    public function setToken($token, $tokenSecret) {
        $this->token = $token;
        $this->tokenSecret = $tokenSecret;
        return $this;
    }

}
