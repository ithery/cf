<?php

namespace Cresenity\Demo\Api\Model;

/**
 * See /docs/api/oauth. Uses CModel_ArrayDriver_ArrayDriverTrait - see
 * /docs/basic/database ("Array-Backed Models") for how that works.
 */
class OauthRefreshToken extends \CApi_OAuth_Model_OAuthRefreshToken {
    use \CModel_ArrayDriver_ArrayDriverTrait;

    protected $guarded = ['oauth_refresh_token_id'];

    protected $schema = [
        'oauth_access_token_id' => 'integer',
        'token' => 'string',
        'revoked' => 'boolean',
        'expires_at' => 'dateTime',
    ];
}
