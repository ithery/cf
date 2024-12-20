<?php
class CApi_OAuth_Model_OAuthClient extends CModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_client';

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'grant_types' => 'array',
        'personal_access_client' => 'bool',
        'password_client' => 'bool',
        'revoked' => 'bool',
    ];

    /**
     * The temporary plain-text client secret.
     *
     * @var null|string
     */
    protected $plainSecret;

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            // if (CF::config('api.oauth.client_uuids')) {
            //     $model->{$model->getKeyName()} = $model->{$model->getKeyName()} ?: (string) cstr::orderedUuid();
            // }
        });
    }

    /**
     * Get the user that the client belongs to.
     *
     * @return CModel_Relation_BelongsTo
     */
    public function user() {
        if ($this->user_type) {
            return $this->morphTo('user');
        }
        $provider = $this->provider ?: CF::config('auth.guards.api.provider');

        return $this->belongsTo(
            CF::config("auth.providers.{$provider}.model")
        );
    }

    /**
     * Get all of the authentication codes for the client.
     *
     * @return \CModel_Relation_HasMany
     */
    public function oauthAuthCodes() {
        return $this->hasMany(CApi::oauth()->authCodeModel(), 'oauth_client_id');
    }

    /**
     * Get all of the tokens that belong to the client.
     *
     * @return \CModel_Relation_HasMany
     */
    public function oauthAccessToken() {
        return $this->hasMany(CApi::oauth()->tokenModel(), 'oauth_client_id');
    }

    /**
     * The temporary non-hashed client secret.
     *
     * This is only available once during the request that created the client.
     *
     * @return null|string
     */
    public function getPlainSecretAttribute() {
        return $this->plainSecret;
    }

    /**
     * Set the value of the secret attribute.
     *
     * @param null|string $value
     *
     * @return void
     */
    public function setSecretAttribute($value) {
        $this->plainSecret = $value;

        if (is_null($value) || !CApi::oauth()->hashesClientSecrets) {
            $this->attributes['secret'] = $value;

            return;
        }

        $this->attributes['secret'] = password_hash($value, PASSWORD_BCRYPT);
    }

    /**
     * Determine if the client is a "first party" client.
     *
     * @return bool
     */
    public function firstParty() {
        return $this->personal_access_client || $this->password_client;
    }

    /**
     * Determine if the client should skip the authorization prompt.
     *
     * @return bool
     */
    public function skipsAuthorization() {
        return false;
    }

    /**
     * Determine if the client is a confidential client.
     *
     * @return bool
     */
    public function confidential() {
        return !empty($this->secret);
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType() {
        return CApi::oauth()->clientUuids() ? 'string' : $this->keyType;
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing() {
        return CApi::oauth()->clientUuids() ? false : $this->incrementing;
    }

    /**
     * Get the current connection name for the model.
     *
     * @return null|string
     */
    public function getConnectionName() {
        return CF::config('api.oauth.storage.database.connection') ?: $this->connection;
    }
}
