<?php

class CModel_AccessToken {
    /**
     * The personal access client model class name.
     *
     * @var string
     */
    public static $accessTokenModel = CModel_AccessToken_AccessTokenModel::class;

    /**
     * A callback that can add to the validation of the access token.
     *
     * @var null|callable
     */
    public static $accessTokenAuthenticationCallback;

    /**
     * Indicates if Sanctum's migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * Set the personal access token model name.
     *
     * @param string $model
     *
     * @return void
     */
    public static function useAccessTokenModel($model) {
        static::$accessTokenModel = $model;
    }

    /**
     * Specify a callback that should be used to authenticate access tokens.
     *
     * @param callable $callback
     *
     * @return void
     */
    public static function authenticateAccessTokensUsing(callable $callback) {
        static::$accessTokenAuthenticationCallback = $callback;
    }

    /**
     * Determine if Sanctum's migrations should be run.
     *
     * @return bool
     */
    public static function shouldRunMigrations() {
        return static::$runsMigrations;
    }

    /**
     * Configure Sanctum to not register its migrations.
     *
     * @return static
     */
    public static function ignoreMigrations() {
        static::$runsMigrations = false;

        return new static();
    }

    /**
     * Get the token model class name.
     *
     * @return string
     */
    public static function accessTokenModel() {
        return static::$accessTokenModel;
    }
}
