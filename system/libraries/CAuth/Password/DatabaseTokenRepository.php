<?php

class CAuth_Password_DatabaseTokenRepository implements CAuth_Contract_TokenRepositoryInterface {
    /**
     * The database connection instance.
     *
     * @var \CDatabase
     */
    protected $connection;

    /**
     * The Hasher implementation.
     *
     * @var \CCrypt_HasherInterface
     */
    protected $hasher;

    /**
     * The token database table.
     *
     * @var string
     */
    protected $table;

    /**
     * The hashing key.
     *
     * @var string
     */
    protected $hashKey;

    /**
     * The number of seconds a token should last.
     *
     * @var int
     */
    protected $expires;

    /**
     * Minimum number of seconds before re-redefining the token.
     *
     * @var int
     */
    protected $throttle;

    /**
     * Create a new token repository instance.
     *
     * @param \CDatabase              $connection
     * @param \CCrypt_HasherInterface $hasher
     * @param string                  $table
     * @param string                  $hashKey
     * @param int                     $expires
     * @param int                     $throttle
     *
     * @return void
     */
    public function __construct(
        CDatabase $connection,
        CCrypt_HasherInterface $hasher,
        $table,
        $hashKey,
        $expires = 60,
        $throttle = 60
    ) {
        $this->table = $table;
        $this->hasher = $hasher;
        $this->hashKey = $hashKey;
        $this->expires = $expires * 60;
        $this->connection = $connection;
        $this->throttle = $throttle;
    }

    /**
     * Create a new token record.
     *
     * @param \CAuth_Contract_CanResetPasswordInterface $user
     *
     * @return string
     */
    public function create(CAuth_Contract_CanResetPasswordInterface $user) {
        $email = $user->getEmailForPasswordReset();

        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        $this->getTable()->insert($this->getPayload($email, $token));

        return $token;
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param \CAuth_Contract_CanResetPasswordInterface $user
     *
     * @return int
     */
    protected function deleteExisting(CAuth_Contract_CanResetPasswordInterface $user) {
        return $this->getTable()->where('email', $user->getEmailForPasswordReset())->delete();
    }

    /**
     * Build the record payload for the table.
     *
     * @param string $email
     * @param string $token
     *
     * @return array
     */
    protected function getPayload($email, $token) {
        return ['email' => $email, 'token' => $this->hasher->make($token), 'created_at' => new CCarbon()];
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param \CAuth_Contract_CanResetPasswordInterface $user
     * @param string                                    $token
     *
     * @return bool
     */
    public function exists(CAuth_Contract_CanResetPasswordInterface $user, $token) {
        $record = (array) $this->getTable()->where(
            'email',
            $user->getEmailForPasswordReset()
        )->first();

        return $record
               && !$this->tokenExpired($record['created_at'])
                 && $this->hasher->check($token, $record['token']);
    }

    /**
     * Determine if the token has expired.
     *
     * @param string $createdAt
     *
     * @return bool
     */
    protected function tokenExpired($createdAt) {
        return CCarbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param \CAuth_Contract_CanResetPasswordInterface $user
     *
     * @return bool
     */
    public function recentlyCreatedToken(CAuth_Contract_CanResetPasswordInterface $user) {
        $record = (array) $this->getTable()->where(
            'email',
            $user->getEmailForPasswordReset()
        )->first();

        return $record && $this->tokenRecentlyCreated($record['created_at']);
    }

    /**
     * Determine if the token was recently created.
     *
     * @param string $createdAt
     *
     * @return bool
     */
    protected function tokenRecentlyCreated($createdAt) {
        if ($this->throttle <= 0) {
            return false;
        }

        return CCarbon::parse($createdAt)->addSeconds(
            $this->throttle
        )->isFuture();
    }

    /**
     * Delete a token record by user.
     *
     * @param \CAuth_Contract_CanResetPasswordInterface $user
     *
     * @return void
     */
    public function delete(CAuth_Contract_CanResetPasswordInterface $user) {
        $this->deleteExisting($user);
    }

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired() {
        $expiredAt = CCarbon::now()->subSeconds($this->expires);

        $this->getTable()->where('created_at', '<', $expiredAt)->delete();
    }

    /**
     * Create a new token for the user.
     *
     * @return string
     */
    public function createNewToken() {
        return hash_hmac('sha256', cstr::random(40), $this->hashKey);
    }

    /**
     * Get the database connection instance.
     *
     * @return \CDatabase
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Begin a new database query against the table.
     *
     * @return \CDatabase_Query_Builder
     */
    protected function getTable() {
        return $this->connection->table($this->table);
    }

    /**
     * Get the hasher instance.
     *
     * @return \CCrypt_HasherInterface
     */
    public function getHasher() {
        return $this->hasher;
    }
}
