<?php

namespace Cresenity\Demo\Api\Model;

/**
 * See /docs/api/oauth. Uses CModel_ArrayDriver_ArrayDriverTrait - see
 * /docs/basic/database ("Array-Backed Models") for how that works.
 */
class OauthAuthCode extends \CApi_OAuth_Model_OAuthAuthCode {
    use \CModel_ArrayDriver_ArrayDriverTrait;

    protected $primaryKey = 'oauth_code_id';

    protected $guarded = ['oauth_code_id'];

    protected $schema = [
        'user_id' => 'integer',
        'user_type' => 'string',
        'code' => 'string',
        'oauth_client_id' => 'integer',
        'scopes' => 'text',
        'revoked' => 'boolean',
        'expires_at' => 'dateTime',
    ];
}
