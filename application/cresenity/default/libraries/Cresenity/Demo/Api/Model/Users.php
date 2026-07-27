<?php

namespace Cresenity\Demo\Api\Model;

/**
 * Illustrates the `findAndValidateForOAuth()` hook that
 * `CApi_OAuth_Bridge_UserRepository` looks for when handling the password
 * grant. See /docs/api/oauth ("Password Grant: Validating Credentials").
 *
 * Uses CModel_ArrayDriver_ArrayDriverTrait (see /docs/basic/database) so this
 * whole example runs standalone, without needing the app's real database -
 * seeded via $rows instead of $schema since we have fixed demo data anyway
 * (username "demo@example.com", password "password").
 *
 * @method static \CModel_Query|static where(...$args)
 */
class Users extends \CModel implements \CAuth_AuthenticatableInterface {
    use \CAuth_Concern_AuthenticatableTrait;
    use \CModel_ArrayDriver_ArrayDriverTrait;

    protected $table = 'users';

    protected $guarded = ['user_id'];

    protected $rows = [
        [
            'user_id' => 1,
            'username' => 'demo@example.com',
            // bcrypt hash of "password" - password_hash('password', PASSWORD_BCRYPT)
            'password' => '$2y$10$.ijIb4jnfFVryvgqTDV15.32Gt.PYtTECcFRd56tmdEiyszi3BEAm',
            'status' => 1,
        ],
    ];

    /**
     * Used by CApi_OAuth_Bridge_UserRepository for the OAuth2 password grant.
     *
     * @param string $username
     * @param string $password
     *
     * @throws \CAuth_Exception_AuthorizationException
     *
     * @return static
     */
    public static function findAndValidateForOAuth($username, $password) {
        $user = static::where('username', '=', $username)->first();

        if ($user && \c::hash('bcrypt')->check($password, $user->password)) {
            if ($user->status > 0) {
                return $user;
            }

            throw new \CAuth_Exception_AuthorizationException('User is inactive.');
        }

        throw new \CAuth_Exception_AuthorizationException('Invalid username or password.');
    }
}
