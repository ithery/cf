<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_Manager {

    /**
     *
     * @var CComponent_Manager 
     */
    protected static $instance;
    protected $listeners = [];
    protected $componentAliases = [];
    protected $queryParamsForTesting = [];
    public static $isLivewireRequestTestingOverride;
    protected static $redirector = null;

    /**
     * 
     * @return CComponent_Manager
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function __construct() {
        
    }

    public function component($alias, $viewClass = null) {
        if (is_null($viewClass)) {
            $viewClass = $alias;
            $alias = $viewClass::getName();
        }

        $this->componentAliases[$alias] = $viewClass;
    }

    public function getAlias($class, $default = null) {
        $alias = array_search($class, $this->componentAliases);

        return $alias === false ? $default : $alias;
    }

    public function getClass($alias) {
        $finder = CComponent_Finder::instance();


        $class = carr::get($this->componentAliases, $alias);

        if (!$class) {
            $class = $finder->find($alias);
        }

        if (!$class) {
            $class = $finder->build()->find($alias);
        }

        c::throwUnless($class, new CComponent_Exception_ComponentNotFoundException(
                        "Unable to find component: [{$alias}]"
        ));

        return $class;
    }

    public function getInstance($component, $id) {
        $componentClass = $this->getClass($component);

        c::throwUnless(class_exists($componentClass), new CComponent_Exception_ComponentNotFoundException(
                        "Component [{$component}] class not found: [{$componentClass}]"
        ));

        return new $componentClass($id);
    }

    public function mount($name, $params = []) {
        // This is if a user doesn't pass params, BUT passes key() as the second argument.
        if (is_string($params))
            $params = [];

        $id = cstr::random(20);

        if (class_exists($name)) {
            $name = $name::getName();
        }

        $response = CComponent_LifecycleManager::fromInitialRequest($name, $id)
                ->initialHydrate()
                ->mount($params)
                ->renderToView()
                ->initialDehydrate()
                ->toInitialResponse();


        return $response;
    }

    public function dummyMount($id, $tagName) {
        return "<{$tagName} cf:id=\"{$id}\"></{$tagName}>";
    }

    public function test($name, $params = []) {
        return new TestableLivewire($name, $params, $this->queryParamsForTesting);
    }

    public function visit($browser, $class, $queryString = '') {
        $url = '/component-dusk/' . urlencode($class) . $queryString;

        return $browser->visit($url)->waitForLivewireToLoad();
    }

    public function actingAs(Authenticatable $user, $driver = null) {
        // This is a helper to be used during testing.

        if (isset($user->wasRecentlyCreated) && $user->wasRecentlyCreated) {
            $user->wasRecentlyCreated = false;
        }

        auth()->guard($driver)->setUser($user);

        auth()->shouldUse($driver);

        return $this;
    }

    public function styles($options = []) {
        $debug = config('app.debug');

        $styles = $this->cssAssets();

        // HTML Label.
        $html = $debug ? ['<!-- Livewire Styles -->'] : [];

        // CSS assets.
        $html[] = $debug ? $styles : $this->minify($styles);

        return implode("\n", $html);
    }

    public function scripts($options = []) {
        $debug = CF::config('app.debug');

        $scripts = $this->javaScriptAssets($options);

        // HTML Label.
        $html = $debug ? ['<!-- Livewire Scripts -->'] : [];

        // JavaScript assets.
        $html[] = $debug ? $scripts : $this->minify($scripts);

        return implode("\n", $html);
    }

    protected function cssAssets() {
        return <<<HTML
<style>
    [wire\:loading], [wire\:loading\.delay], [wire\:loading\.inline-block], [wire\:loading\.inline], [wire\:loading\.block], [wire\:loading\.flex], [wire\:loading\.table], [wire\:loading\.grid] {
        display: none;
    }
    [wire\:offline] {
        display: none;
    }
    [wire\:dirty]:not(textarea):not(input):not(select) {
        display: none;
    }
    input:-webkit-autofill, select:-webkit-autofill, textarea:-webkit-autofill {
        animation-duration: 50000s;
        animation-name: livewireautofill;
    }
    @keyframes livewireautofill { from {} }
</style>
HTML;
    }

    protected function javaScriptAssets($options) {
        $jsonEncodedOptions = $options ? json_encode($options) : '';

        $devTools = null;

        if (config('app.debug')) {
            $devTools = 'window.livewire.devTools(true);';
        }

        $appUrl = CF::config('component.asset_url', rtrim(carr::get($options, 'asset_url', ''), '/'));

        $csrf = csrf_token();

        $manifest = json_decode(file_get_contents(__DIR__ . '/../dist/manifest.json'), true);
        $versionedFileName = $manifest['/livewire.js'];

        // Default to dynamic `livewire.js` (served by a Laravel route).
        $fullAssetPath = "{$appUrl}/livewire{$versionedFileName}";
        $assetWarning = null;

        $nonce = isset($options['nonce']) ? "nonce=\"{$options['nonce']}\"" : '';

        // Use static assets if they have been published
        if (file_exists(public_path('vendor/livewire/manifest.json'))) {
            $publishedManifest = json_decode(file_get_contents(public_path('vendor/livewire/manifest.json')), true);
            $versionedFileName = $publishedManifest['/livewire.js'];

            $fullAssetPath = ($this->isOnVapor() ? config('app.asset_url') : $appUrl) . '/vendor/livewire' . $versionedFileName;

            if ($manifest !== $publishedManifest) {
                $assetWarning = <<<'HTML'
<script {$nonce}>
    console.warn("Livewire: The published Livewire assets are out of date\n See: https://laravel-livewire.com/docs/installation/")
</script>
HTML;
            }
        }

        // Adding semicolons for this JavaScript is important,
        // because it will be minified in production.
        return <<<HTML
{$assetWarning}
<script src="{$fullAssetPath}" data-turbolinks-eval="false"></script>
<script data-turbolinks-eval="false"{$nonce}>
    if (window.livewire) {
        console.warn('Livewire: It looks like Livewire\'s @livewireScripts JavaScript assets have already been loaded. Make sure you aren\'t loading them twice.')
    }
    window.livewire = new Livewire({$jsonEncodedOptions});
    {$devTools}
    window.Livewire = window.livewire;
    window.livewire_app_url = '{$appUrl}';
    window.livewire_token = '{$csrf}';
    /* Make sure Livewire loads first. */
    if (window.Alpine) {
        /* Defer showing the warning so it doesn't get buried under downstream errors. */
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(function() {
                console.warn("Livewire: It looks like AlpineJS has already been loaded. Make sure Livewire\'s scripts are loaded before Alpine.\\n\\n Reference docs for more info: http://laravel-livewire.com/docs/alpine-js")
            })
        });
    }
    /* Make Alpine wait until Livewire is finished rendering to do its thing. */
    window.deferLoadingAlpine = function (callback) {
        window.addEventListener('livewire:load', function () {
            callback();
        });
    };
    document.addEventListener("DOMContentLoaded", function () {
        window.livewire.start();
    });
</script>
HTML;
    }

    protected function minify($subject) {
        return preg_replace('~(\v|\t|\s{2,})~m', '', $subject);
    }

    public function isLivewireRequest() {
        if (static::$isLivewireRequestTestingOverride) {
            return true;
        }

        return CHTTP::request()->hasHeader('X-Livewire');
    }

    public function getRootElementTagName($dom) {
        preg_match('/<([a-zA-Z0-9\-]*)/', $dom, $matches, PREG_OFFSET_CAPTURE);

        return $matches[1][0];
    }

    public function dispatch($event, ...$params) {
        foreach (carr::get($this->listeners, $event, []) as $listener) {
            $listener(...$params);
        }
    }

    public function listen($event, $callback) {
        $this->listeners[$event][] = $callback;
    }

    public function isOnVapor() {
        return carr::get($_ENV, 'SERVER_SOFTWARE') === 'vapor';
    }

    public function withQueryParams($queryParams) {
        $this->queryParamsForTesting = $queryParams;

        return $this;
    }

    /**
     * 
     * @param string $alias
     * @param string $class
     */
    public function registerComponent($alias, $class) {
        return $this->component($alias, $class);
    }

    public function controllerHandler($args) {
        if (is_array($args) && count($args) > 0) {
            $method = carr::get($args, 0);
            $args = array_slice($args, 1);
            $handler = new CComponent_ControllerHandler($method);
            return $handler->execute($method);
        }

        return CF::show404();
    }

    public function redirector() {
        if ($this->redirector == null) {
            $this->redirector = new CComponent_Redirector();
        }
        return $this->redirector;
    }

    public function getHtml($componentName,$params=[]) {
        if (!isset($_instance)) {
            $html = CApp::component()->mount($componentName,$params)->html();
        } elseif ($_instance->childHasBeenRendered($cachedKey)) {
            $componentId = $_instance->getRenderedChildComponentId($cachedKey);
            $componentTag = $_instance->getRenderedChildComponentTagName($cachedKey);
            $html = CApp::component()->dummyMount($componentId, $componentTag);
            $_instance->preserveRenderedChild($cachedKey);
        } else {
            $response = CApp::component()->mount($componentName,$params);
            $html = $response->html();
            $_instance->logRenderedChild($cachedKey, $response->id(), CApp::component()->getRootElementTagName($html));
        }
        return $html;
    }

}
