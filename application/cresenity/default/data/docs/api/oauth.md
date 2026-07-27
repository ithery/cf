# API - OAuth2 (CApi_OAuth)

`CApi_OAuth` is CF's wrapper around `league/oauth2-server` (the same library
Laravel Passport is built on) - a real OAuth2 authorization + resource
server: RSA-signed JWT access tokens, refresh tokens, and all four grant
types (authorization code, client credentials, password, personal access).

Use it whenever an API group needs to know *who* (which client, and
optionally which specific user) is calling - as opposed to a flat shared
secret. This page's example is a real, runnable group shipped with this docs
app: `Cresenity\Demo\Api\Widget` at
`application/cresenity/default/libraries/Cresenity/Demo/Api/` - open it
alongside this page.

---

### 1. Generate the Encryption Keys

League's server signs tokens with an RSA keypair. Generate it once per app
(run from the app directory):

```bash
phpcf api:oauth:key
```

This writes `oauth-private.key` / `oauth-public.key` to the app's `DOCROOT`
(pass `--force` to overwrite, `--length=` to change the key size, default
4096). Unless you call `$oauth->loadKeysFrom($path)`, that's where
`CApi_OAuth::keyPath()` always looks - no further config needed for a
single-app setup.

---

### 2. Database Tables

A real app needs five tables, all with the standard CF audit columns
(`created`/`createdby`/`updated`/`updatedby`/`deleted`/`deletedby`/`status`)
plus an `org_id` column (FK to `org`) for multi-tenant scoping:

```sql
CREATE TABLE `oauth_client` (
  `oauth_client_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_type` varchar(255) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secret` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `personal_access_client` int(1) DEFAULT 0,
  `password_client` int(1) DEFAULT 0,
  `revoked` int(1) DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `deletedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`oauth_client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `oauth_access_token` (
  `oauth_access_token_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `oauth_client_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_type` varchar(255) DEFAULT NULL,
  `token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` int(1) DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `deletedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`oauth_access_token_id`),
  KEY `org_id` (`org_id`),
  KEY `oauth_client_id` (`oauth_client_id`),
  KEY `idx_token` (`token`),
  CONSTRAINT `oauth_access_token_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `org` (`org_id`),
  CONSTRAINT `oauth_access_token_ibfk_2` FOREIGN KEY (`oauth_client_id`) REFERENCES `oauth_client` (`oauth_client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `oauth_refresh_token` (
  `oauth_refresh_token_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `oauth_access_token_id` bigint(20) unsigned DEFAULT NULL,
  `token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` int(1) DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `deletedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`oauth_refresh_token_id`),
  KEY `org_id` (`org_id`),
  KEY `oauth_access_token_id` (`oauth_access_token_id`),
  KEY `idx_token` (`token`),
  CONSTRAINT `oauth_refresh_token_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `org` (`org_id`),
  CONSTRAINT `oauth_refresh_token_ibfk_2` FOREIGN KEY (`oauth_access_token_id`) REFERENCES `oauth_access_token` (`oauth_access_token_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `oauth_auth_code` (
  `oauth_code_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_type` varchar(255) DEFAULT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `oauth_client_id` bigint(20) unsigned DEFAULT NULL,
  `scopes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` int(1) DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `deletedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`oauth_code_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `oauth_personal_access_client` (
  `oauth_personal_access_client_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `oauth_client_id` bigint(20) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `deletedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`oauth_personal_access_client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Note the primary key naming: `oauth_client`/`oauth_access_token`/
`oauth_refresh_token`/`oauth_personal_access_client` all follow CF's
`{table}_id` auto-derived convention (see
[Database - Primary Key](/docs/basic/database)), but `oauth_auth_code`'s key
is `oauth_code_id` (not `oauth_auth_code_id`) - so that one model needs an
explicit `$primaryKey` override (shown below).

**For a quick prototype/demo instead of a real migration** - which is
exactly what this page's own example does - skip all of this and use
`CModel_ArrayDriver_ArrayDriverTrait` on each model instead (auto-creates its
own SQLite-backed table from a `$schema`/`$rows` property, no database setup
at all). See [Database - Array-Backed Models](/docs/basic/database) and
`Cresenity\Demo\Api\Model\*` for the full working version of every model
below built that way.

Either way, create thin per-app model subclasses so relations/traits match
the rest of your app's models - the classes themselves need no logic beyond
`extends`:

```php
// Cresenity/Demo/Api/Model/OauthClient.php (excerpt - the real file seeds
// $rows instead of relying on a migrated table, see above)
namespace Cresenity\Demo\Api\Model;

class OauthClient extends \CApi_OAuth_Model_OAuthClient {
    protected $guarded = ['oauth_client_id'];
}
```

```php
// Cresenity/Demo/Api/Model/OauthAuthCode.php
namespace Cresenity\Demo\Api\Model;

class OauthAuthCode extends \CApi_OAuth_Model_OAuthAuthCode {
    protected $primaryKey = 'oauth_code_id'; // see the naming note above
    protected $guarded = ['oauth_code_id'];
}
```

(`OauthAccessToken`, `OauthRefreshToken`, `OauthPersonalAccessClient` follow
the same one-line-body shape - see the actual files.)

---

### 3. Configure the Group

```php
// default/config/api.php
return [
    'groups' => [
        'widget' => [
            'error_format' => [
                'errCode' => ':code',
                'errMessage' => ':message',
                'data' => [],
            ],
            'debug' => !MyApp::isProduction(),
            'oauth' => [
                'private_key' => null, // null = fall back to DOCROOT/oauth-private.key
                'public_key' => null,
            ],
        ],
    ],
];
```

Then bind your model classes and token lifetimes via the dispatcher's
`withOAuth()` callback - see `Cresenity\Demo\Api\Widget::dispatcher()`:

```php
// Cresenity/Demo/Api/Widget.php
public function dispatcher() {
    return \c::api('widget')->createDispatcher()
        ->setPrefix('api/widget')
        ->setMethodNamespace('\\Cresenity\\Demo\\Api\\Widget\\Method')
        ->withOAuth(function (\CApi_OAuth $oauth) {
            $oauth->useTokenModel(Model\OauthAccessToken::class);
            $oauth->useClientModel(Model\OauthClient::class);
            $oauth->useAuthCodeModel(Model\OauthAuthCode::class);
            $oauth->useRefreshTokenModel(Model\OauthRefreshToken::class);
            $oauth->usePersonalAccessClientModel(Model\OauthPersonalAccessClient::class);

            $oauth->tokensExpireIn(\c::now()->addDays(1));
            $oauth->refreshTokensExpireIn(\c::now()->addDays(30));
            $oauth->personalAccessTokensExpireIn(\c::now()->addMonths(6));
        });
}
```

If several unrelated parts of the app need to reach `CApi::oauth('widget')`
independently (not just this one dispatcher), binding the same calls in the
app's `Bootstrap::boot()` instead works identically - `withOAuth()`'s
callback and a Bootstrap method are just two places to run the same setup.

Either way, `->withOAuth()` on the dispatcher is what actually turns the
group's OAuth on - it also auto-routes `{prefix}/oauth/token` to
`CApi_OAuth_Method_Token` (League's `respondToAccessTokenRequest()` under the
hood), so **you never write a token-endpoint controller yourself**.

---

### 4. Create a Client

```bash
phpcf api:oauth:client --group=widget --password --name="Widget Password Grant Client" --provider=users
```

Prints a `Client ID` and `Client secret` - save these, the secret is shown
**once**. Four client kinds, matching League's four grant types:

| Flag         | Grant type          | Use case                                            |
|--------------|----------------------|------------------------------------------------------|
| (none)       | authorization_code   | Third-party apps, browser redirect flow, needs `--user_id` |
| `--password`  | password             | Your own first-party client (SPA, mobile app) trading a username/password directly for a token |
| `--client`    | client_credentials   | Machine-to-machine, no specific user (CLI tools, server-to-server) |
| `--personal`  | personal_access_token | Long-lived tokens a user generates for themselves (like a GitHub PAT) |

A client can be flagged for more than one grant. The example's seeded client
(`Cresenity\Demo\Api\Model\OauthClient`'s `$rows`) is `password_client => 1`.

---

### 5. Password Grant: Validating Credentials

For the `password` grant, League needs to check a username/password pair
against your user model. `CApi_OAuth_Bridge_UserRepository` looks for a
special method on the auth provider's model
(`auth.providers.{provider}.model`, matching the `--provider` you picked
when creating the client) - see `Cresenity\Demo\Api\Model\Users`:

```php
// Cresenity/Demo/Api/Model/Users.php (excerpt)
class Users extends \CModel implements \CAuth_AuthenticatableInterface {
    /**
     * Used by CApi_OAuth_Bridge_UserRepository for the OAuth2 password grant.
     */
    public static function findAndValidateForOAuth($username, $password) {
        $user = static::where('username', '=', $username)->first();

        if ($user && \c::hash('bcrypt')->check($password, $user->password)) {
            if ($user->status > 0) {
                return $user;
            }
            throw new \CAuth_Exception_AuthorizationException('User is inactive.');
        }

        throw new \CAuth_Exception_AuthorizationException('Invalid username or password.');
    }
}
```

If `findAndValidateForOAuth` isn't defined, the bridge falls back to looking
the user up by an `email` column and checking the password against
`getAuthPassword()` with the provider's configured hasher - defining the
method explicitly is recommended so you control the lookup field and get a
clean error message instead of a generic OAuth failure.

Requesting a token from a browser/SPA is then a plain POST, no custom
controller needed. Using the example's seeded demo data (client ID `1`,
secret `widget-example-demo-secret`, user `demo@example.com` / `password`):

```
POST /api/widget/oauth/token
Content-Type: application/json

{
  "grant_type": "password",
  "client_id": "1",
  "client_secret": "widget-example-demo-secret",
  "username": "demo@example.com",
  "password": "password",
  "scope": ""
}
```

which responds with the standard OAuth2 token payload (`access_token`,
`refresh_token`, `expires_in`, `token_type`).

---

### 6. Protecting Endpoints

Two abstract middlewares ship with the framework, both requiring you to
implement `validateCredentials($token)` / `validateScopes($token, $scopes)`:

- `CApi_OAuth_Middleware_CheckClientCredentials` - accepts *any* valid,
  unrevoked, unexpired token, regardless of which user (if any) it belongs
  to. Use this as the default for a group where most endpoints don't need to
  know the caller.
- For endpoints that specifically need the calling *user* (only meaningful
  for tokens issued via the password grant, since client_credentials tokens
  have no user), extend it further -
  `Cresenity\Demo\Api\Widget\Middleware\UserCredentialsMiddleware`:

```php
// Cresenity/Demo/Api/Widget/Middleware/UserCredentialsMiddleware.php
namespace Cresenity\Demo\Api\Widget\Middleware;

class UserCredentialsMiddleware extends \CApi_OAuth_Middleware_CheckClientCredentials {
    protected function validateCredentials($token) {
        parent::validateCredentials($token);

        $user = $token->user;
        if (!$user) {
            throw new \CAuth_Exception_AuthenticationException('This endpoint requires a user-authenticated (password grant) token.');
        }

        $this->request->setUserResolver(function () use ($user) {
            return $user;
        });
    }
}
```

Then apply it in the group's `MethodAbstract` -
`Cresenity\Demo\Api\Widget\MethodAbstract`:

```php
// Cresenity/Demo/Api/Widget/MethodAbstract.php
namespace Cresenity\Demo\Api\Widget;

abstract class MethodAbstract extends \CApi_OAuth_MethodAbstract {
    public function __construct() {
        parent::__construct();
        $this->middleware(Middleware\UserCredentialsMiddleware::class);
    }
}
```

Every method under that abstract can now call `$this->request()->user()` (or
`c::user()`, depending on how your request/user resolver is wired) and get
back the actual user row the token was issued for - see
`Cresenity\Demo\Api\Widget\Method\Resize\Image::execute()`, covered in
[Generating Docs](/docs/api/docs-generation).

---

### Full Checklist

1. `phpcf api:oauth:key`
2. Apply the schema above (or use `CModel_ArrayDriver_ArrayDriverTrait` for a
   prototype), add thin `Oauth*` model subclasses
3. Add the group to `default/config/api.php` (`oauth` key with null key paths)
4. Bind models + token lifetimes via the dispatcher's `withOAuth()` callback
   (or Bootstrap, if several places need `CApi::oauth($group)`)
5. `->withOAuth()` on the dispatcher
6. `phpcf api:oauth:client --group=... --password --provider=...`
7. Add `findAndValidateForOAuth()` to your user model
8. Write a `CheckClientCredentials`-based (+ optional user-aware) middleware,
   apply it in the group's `MethodAbstract`

From here, see [Generating Docs](/docs/api/docs-generation) to get an
importable OpenAPI spec and a Swagger UI "try it out" page for the group.
