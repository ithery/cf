<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class COAuth {

    protected static $clientRepository;

    /**
     * Get the auth code model class name.
     *
     * @return string
     */
    public static function authCodeModel() {
        return CF::config('oauth.model.authCode');
    }

    /**
     * Get the client model class name.
     *
     * @return string
     */
    public static function clientModel() {
        return CF::config('oauth.model.client');
    }

    /**
     * Get the personal access client model class name.
     *
     * @return string
     */
    public static function personalAccessClientModel() {
        return CF::config('oauth.model.personalAccessClient');
    }

    /**
     * Get the token model class name.
     *
     * @return string
     */
    public static function accessTokenModel() {
        return CF::config('oauth.model.accessToken');
    }

    /**
     * Get the refresh token model class name.
     *
     * @return string
     */
    public static function refreshTokenModel() {
        return CF::config('oauth.model.refreshToken');
    }

    /**
     * Get a new client model instance.
     *
     * @return COAuth_Model_Client
     */
    public static function client() {
        $clientModel = static::clientModel();
        return new $clientModel;
    }

    public static function clientRepository() {
        if (static::$clientRepository == null) {
            static::$clientRepository = new COAuth_Repository_ClientRepository();
        }
        return static::$clientRepository;
    }

}
