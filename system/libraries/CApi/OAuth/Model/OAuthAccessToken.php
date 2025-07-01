<?php
/**
 * @property      int                                 $status
 * @property      null|string                         $createdby
 * @property      null|string                         $updatedby
 * @property      null|CCarbon|\Carbon\Carbon         $updated
 * @property      null|CCarbon|\Carbon\Carbon         $created
 * @property      null|CCarbon|\Carbon\Carbon         $expires_at
 * @property      null|bool                           $revoked
 * @property      null|array                          $scopes
 * @property      null|string                         $name
 * @property      null|string                         $token
 * @property      null|string                         $user_type
 * @property      null|int                            $user_id
 * @property      null|int                            $oauth_client_id
 * @property      null|int                            $org_id
 * @property-read int                                 $oauth_access_token
 * @property-read null|CAuth_AuthenticatableInterface $user
 */
class CApi_OAuth_Model_OAuthAccessToken extends CModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_access_token';

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'scopes' => 'array',
        'revoked' => 'bool',
        'expires_at' => 'datetime'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expires_at',
    ];

    public static function boot() {
        parent::boot();

        static::deleting(function (CApi_OAuth_Model_OAuthAccessToken $model) {
            if (static::usesSoftDelete() && $model->isForceDeleting()) {
                $model->oauthRefreshToken->each->forceDelete();
            } else {
                $model->oauthRefreshToken->each->delete();
            }
        });
    }

    /**
     * Get the refresh token has many.
     *
     * @return \CModel_Relation_HasMany
     */
    public function oauthRefreshToken() {
        return $this->hasMany(CApi::oauth()->refreshTokenModel());
    }

    /**
     * Get the client that the token belongs to.
     *
     * @return \CModel_Relation_BelongsTo
     */
    public function oauthClient() {
        return $this->belongsTo(CApi::oauth()->clientModel());
    }

    /**
     * Get the user that the token belongs to.
     *
     * @return \CModel_Relation_BelongsTo
     */
    public function user() {
        $userModel = CApi::oauth()->getUserModelFromProvider();
        if ($userModel == null) {
            $provider = CF::config('auth.guards.api.provider');

            $modelName = CF::config('auth.providers.' . $provider . '.model');
            $model = new $modelName();
            /** @var CModel $model */

            return $this->belongsTo($modelName, 'user_id', $model->getKeyName());
        } else {
            return $this->morphTo('user');
        }
    }

    /**
     * Determine if the token has a given scope.
     *
     * @param string $scope
     *
     * @return bool
     */
    public function can($scope) {
        if (in_array('*', $this->scopes)) {
            return true;
        }

        $scopes = CApi::oauth()->withInheritedScopes
            ? $this->resolveInheritedScopes($scope)
            : [$scope];

        foreach ($scopes as $scope) {
            if (array_key_exists($scope, array_flip($this->scopes))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve all possible scopes.
     *
     * @param string $scope
     *
     * @return array
     */
    protected function resolveInheritedScopes($scope) {
        $parts = explode(':', $scope);

        $partsCount = count($parts);

        $scopes = [];

        for ($i = 1; $i <= $partsCount; $i++) {
            $scopes[] = implode(':', array_slice($parts, 0, $i));
        }

        return $scopes;
    }

    /**
     * Determine if the token is missing a given scope.
     *
     * @param string $scope
     *
     * @return bool
     */
    public function cant($scope) {
        return !$this->can($scope);
    }

    /**
     * Revoke the token instance.
     *
     * @return bool
     */
    public function revoke() {
        return $this->forceFill(['revoked' => true])->save();
    }

    /**
     * Determine if the token is a transient JWT token.
     *
     * @return bool
     */
    public function transient() {
        return false;
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
