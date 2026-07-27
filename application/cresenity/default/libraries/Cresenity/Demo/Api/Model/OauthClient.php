<?php

namespace Cresenity\Demo\Api\Model;

/**
 * Thin app-specific subclass so the model fits alongside the rest of the
 * app's models (traits, guarded fields, PHPDoc). The base class already
 * implements everything CApi_OAuth needs. See /docs/api/oauth.
 *
 * Uses CModel_ArrayDriver_ArrayDriverTrait (same as Cresenity\Demo\Model\Item)
 * so the whole example runs against an auto-created, auto-cached SQLite file
 * under temp/model/array/cache/ - no real database or migration needed.
 * Seeded via $rows with one fixed password-grant client (client_id 1) so the
 * credentials in /docs/api/oauth's token request example are real and stable.
 */
class OauthClient extends \CApi_OAuth_Model_OAuthClient {
    use \CModel_ArrayDriver_ArrayDriverTrait;

    protected $guarded = ['oauth_client_id'];

    protected $rows = [
        [
            'oauth_client_id' => 1,
            'name' => 'Widget Example Password Grant Client',
            'secret' => 'widget-example-demo-secret',
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked' => 0,
        ],
    ];
}
