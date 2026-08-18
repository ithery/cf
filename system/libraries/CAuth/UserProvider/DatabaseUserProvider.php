<?php

use Illuminate\Contracts\Support\Arrayable;

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
     * @var CCrypt_HasherInterface
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
     * @param CDatabase_Connection   $conn
     * @param CCrypt_HasherInterface $hasher
     * @param string                 $table
     *
     * @return void
     */
    public function __construct(CDatabase_Connection $conn, CCrypt_HasherInterface $hasher, $table) {
        $this->conn = $conn;
        $this->table = $table;
        $this->hasher = $hasher;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     *
     * @return null|CAuth_AuthenticatableInterface
     */
    public function retrieveById($identifier) {
        $user = $this->conn->table($this->table)->find($identifier);

        return $this->getGenericUser($user);
    }

    /**
     * Retrieve a user by stdclass object.
     *
     * @param mixed $object
     *
     * @return null|CAuth_AuthenticatableInterface
     */
    public function retrieveByObject($object) {
        $identifier = c::get($object, 'user_id');

        return $this->retrieveById($identifier);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed  $identifier
     * @param string $token
     *
     * @return null|\CAuth_AuthenticatableInterface
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
    public function updateRememberToken($user, $token) {
        $this->conn->table($this->table)
            ->where($user->getAuthIdentifierName(), $user->getAuthIdentifier())
            ->update([$user->getRememberTokenName() => $token]);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     *
     * @return null|\CAuth_AuthenticatableInterface
     */
    public function retrieveByCredentials(array $credentials) {
        if (empty($credentials)
            || (count($credentials) === 1
            && array_key_exists('password', $credentials))
        ) {
            return null;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // generic "user" object that will be utilized by the Guard instances.
        $query = $this->conn->table($this->table);

        foreach ($credentials as $key => $value) {
            if (cstr::contains($key, 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } elseif ($value instanceof Closure) {
                $value($query);
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
     * @return null|CAuth_GenericUser
     */
    protected function getGenericUser($user) {
        if (!is_null($user)) {
            return new CAuth_GenericUser((array) $user);
        }

        return null;
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

    public function hasher() {
        return $this->hasher;
    }
}
