<?php

class CAuth_UserProvider_DatabaseUserProvider extends CAuth_UserProviderAbstract {
    /**
     * The active database connection.
     *
     * @var CDatabase
     */
    protected $conn;

    /**
     * The hasher implementation.
     *
     * @var CCrypt_Hasher
     */
    protected $hasher;

    /**
     * The table containing the users.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new database user provider.
     *
     * @param CDatabase              $conn
     * @param CCrypt_HasherInterface $hasher
     * @param string                 $table
     *
     * @return void
     */
    public function __construct(CDatabase $conn, CCrypt_HasherInterface $hasher, $table) {
        $this->conn = $conn;
        $this->table = $table;
        $this->hasher = $hasher;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     *
     * @return CAuth_AuthenticatableInterface|null
     */
    public function retrieveById($identifier) {
        $user = $this->conn->table($this->table)->find($identifier);

        return $this->getGenericUser($user);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed  $identifier
     * @param string $token
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token) {
        $user = $this->getGenericUser(
            $this->conn->table($this->table)->find($identifier)
        );

        return $user && $user->getRememberToken() && hash_equals($user->getRememberToken(), $token)
                    ? $user : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param CAuth_AuthenticatableInterface $user
     * @param string                         $token
     *
     * @return void
     */
    public function updateRememberToken(CAuth_AuthenticatableInterface $user, $token) {
        $this->conn->table($this->table)
            ->where($user->getAuthIdentifierName(), $user->getAuthIdentifier())
            ->update([$user->getRememberTokenName() => $token]);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials) {
        if (empty($credentials)
            || (count($credentials) === 1
            && array_key_exists('password', $credentials))
        ) {
            return;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // generic "user" object that will be utilized by the Guard instances.
        $query = $this->conn->table($this->table);

        foreach ($credentials as $key => $value) {
            if (cstr::contains($key, 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof CInterface_Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        // Now we are ready to execute the query to see if we have an user matching
        // the given credentials. If not, we will just return nulls and indicate
        // that there are no matching users for these given credential arrays.
        $user = $query->first();

        return $this->getGenericUser($user);
    }

    /**
     * Get the generic user.
     *
     * @param mixed $user
     *
     * @return CAuth_GenericUser|null
     */
    protected function getGenericUser($user) {
        if (!is_null($user)) {
            return new CAuth_GenericUser((array) $user);
        }
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param \CAuth_AuthenticatableInterface $user
     * @param array                           $credentials
     *
     * @return bool
     */
    public function validateCredentials(CAuth_AuthenticatableInterface $user, array $credentials) {
        return $this->hasher->check(
            $credentials['password'],
            $user->getAuthPassword()
        );
    }
}
