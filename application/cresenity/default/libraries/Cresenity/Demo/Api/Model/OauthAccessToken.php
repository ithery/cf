<?php

namespace Cresenity\Demo\Api\Model;

/**
 * See /docs/api/oauth. Uses CModel_ArrayDriver_ArrayDriverTrait - see
 * /docs/basic/database ("Array-Backed Models") for how that works.
 */
class OauthAccessToken extends \CApi_OAuth_Model_OAuthAccessToken {
    use \CModel_ArrayDriver_ArrayDriverTrait;

    protected $guarded = ['oauth_access_token_id'];

    protected $schema = [
        'oauth_client_id' => 'integer',
        'user_id' => 'integer',
        'user_type' => 'string',
        'token' => 'string',
        'name' => 'string',
        'scopes' => 'text',
        'revoked' => 'boolean',
        'expires_at' => 'dateTime',
    ];
}
