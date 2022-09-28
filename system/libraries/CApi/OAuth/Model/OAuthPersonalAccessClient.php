<?php

class CApi_OAuth_Model_OAuthPersonalAccessClient extends CModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_personal_access_client';

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get all of the authentication codes for the client.
     *
     * @return \CModel_Relation_BelongsTo
     */
    public function oauthClient() {
        return $this->belongsTo(CApi::oauth()->clientModel());
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
