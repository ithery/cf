<?php

class CAuth_UserProvider_ModelUserProvider extends CAuth_UserProviderAbstract {
    /**
     * The hasher implementation.
     *
     * @var CCrypt_Hasher
     */
    protected $hasher;

    /**
     * The Eloquent user model.
     *
     * @var string
     */
    protected $model;

    /**
     * Create a new database user provider.
     *
     * @param CCrypt_HasherInterface $hasher
     * @param string                 $model
     *
     * @return void
     */
    public function __construct(CCrypt_HasherInterface $hasher, $model) {
        $this->model = $model;
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
        $model = $this->createModel();

        return $this->newModelQuery($model)
            ->where($model->getAuthIdentifierName(), $identifier)
            ->first();
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed  $identifier
     * @param string $token
     *
     * @return CAuth_AuthenticatableInterface|null
     */
    public function retrieveByToken($identifier, $token) {
        $model = $this->createModel();

        $retrievedModel = $this->newModelQuery($model)->where(
            $model->getAuthIdentifierName(),
            $identifier
        )->first();

        if (!$retrievedModel) {
            return;
        }

        $rememberToken = $retrievedModel->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token)
                        ? $retrievedModel : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param CAuth_AuthenticatableInterface|CModel $user
     * @param string                                $token
     *
     * @return void
     */
    public function updateRememberToken($user, $token) {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     *
     * @return CAuth_AuthenticatableInterface|null
     */
    public function retrieveByCredentials(array $credentials) {
        if (empty($credentials)
            || (count($credentials) === 1
            && cstr::contains($this->firstCredentialKey($credentials), 'password'))
        ) {
            return;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (cstr::contains($key, 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof Cinterface_Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Get the first key from the credential array.
     *
     * @param array $credentials
     *
     * @return string|null
     */
    protected function firstCredentialKey(array $credentials) {
        foreach ($credentials as $key => $value) {
            return $key;
        }
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param CAuth_AuthenticatableInterface $user
     * @param array                          $credentials
     *
     * @return bool
     */
    public function validateCredentials(CAuth_AuthenticatableInterface $user, array $credentials) {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    /**
     * Get a new query builder for the model instance.
     *
     * @param CModel|null $model
     *
     * @return CModel_Query
     */
    protected function newModelQuery($model = null) {
        return is_null($model)
                ? $this->createModel()->newQuery()
                : $model->newQuery();
    }

    /**
     * Create a new instance of the model.
     *
     * @return CModel
     */
    public function createModel() {
        $class = '\\' . ltrim($this->model, '\\');

        return new $class;
    }

    /**
     * Gets the hasher implementation.
     *
     * @return \Illuminate\Contracts\Hashing\Hasher
     */
    public function getHasher() {
        return $this->hasher;
    }

    /**
     * Sets the hasher implementation.
     *
     * @param CCrypt_HasherInterface $hasher
     *
     * @return $this
     */
    public function setHasher(CCrypt_HasherInterface $hasher) {
        $this->hasher = $hasher;

        return $this;
    }

    /**
     * Gets the name of the Eloquent user model.
     *
     * @return string
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Sets the name of the Eloquent user model.
     *
     * @param string $model
     *
     * @return $this
     */
    public function setModel($model) {
        $this->model = $model;

        return $this;
    }
}
