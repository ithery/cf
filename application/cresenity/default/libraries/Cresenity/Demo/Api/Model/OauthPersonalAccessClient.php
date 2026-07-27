<?php

namespace Cresenity\Demo\Api\Model;

/**
 * See /docs/api/oauth. Uses CModel_ArrayDriver_ArrayDriverTrait - see
 * /docs/basic/database ("Array-Backed Models") for how that works.
 */
class OauthPersonalAccessClient extends \CApi_OAuth_Model_OAuthPersonalAccessClient {
    use \CModel_ArrayDriver_ArrayDriverTrait;

    protected $guarded = ['oauth_personal_access_client_id'];

    protected $schema = [
        'oauth_client_id' => 'integer',
    ];
}
