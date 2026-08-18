<?php

namespace Cresenity\Demo\Api;

/**
 * Reference example for the "API" documentation section
 * (see /docs/api/introduction, /docs/api/oauth, /docs/api/docs-generation).
 *
 * Not wired into any controller/route - this exists purely so the docs can
 * point at real, readable source files instead of another (private) app's
 * code that readers of this documentation may not have access to.
 */
class Widget {
    /**
     * @var null|Widget
     */
    protected static $instance;

    /**
     * @return Widget
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @return \CApi_Dispatcher
     */
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

    /**
     * @return array
     */
    public function generateDocs() {
        return \c::api('widget')->createDocsGenerator()
            ->addAnnotationDir(\c::appRoot(['default', 'libraries', 'Cresenity', 'Demo', 'Api', 'Widget', 'Method']))
            ->setConstants([
                'WIDGET_API_VERSION' => '1.0.0',
                'WIDGET_API_BASE_URL' => \c::url('api/widget'),
                'WIDGET_API_TITLE' => 'Widget API (documentation example)',
            ])
            ->setSecuritySchemes([
                'oauth2' => [
                    'type' => 'oauth2',
                    'flows' => [
                        'password' => [
                            'tokenUrl' => \c::url('api/widget/oauth/token'),
                            'scopes' => [],
                        ],
                    ],
                ],
            ])
            ->setOutputDir($this->swaggerDir())
            ->setOutputJsonFile('api-docs.json')
            ->generate();
    }

    /**
     * @return string
     */
    protected function swaggerDir() {
        return \c::appRoot(['default', 'data', 'docs', 'api', 'widget', 'swagger']);
    }

    /**
     * @return string
     */
    public function swaggerJsonPath() {
        return rtrim($this->swaggerDir(), DS) . DS . 'api-docs.json';
    }
}
