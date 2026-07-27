# API - Introduction

`CApi` is CF's framework for building versioned, self-documenting API groups
(a Dingo-API-like layer). An "API group" bundles together a URL prefix, a
method-class namespace, shared middleware, error formatting, and (optionally)
its own OAuth2 server - so one app can expose several independent groups side
by side, each with different auth rules.

This page (and [OAuth2](/docs/api/oauth) / [Generating Docs](/docs/api/docs-generation))
uses a real, runnable example group shipped with this docs app itself:
`Cresenity\Demo\Api\Widget` at
`application/cresenity/default/libraries/Cresenity/Demo/Api/`. Open those
files alongside this page - every snippet below is either lifted directly
from them or a close paraphrase.

---

### Anatomy of a Group

A group is normally wrapped in a small class with two responsibilities:
building its dispatcher, and building its docs generator.

```php
// Cresenity/Demo/Api/Widget.php
namespace Cresenity\Demo\Api;

class Widget {
    public function dispatcher() {
        return \c::api('widget')->createDispatcher()
            ->setPrefix('api/widget')
            ->setMethodNamespace('\\Cresenity\\Demo\\Api\\Widget\\Method');
            // (the real file also chains ->withOAuth(...) here - see /docs/api/oauth)
    }
}
```

- `c::api($group)` resolves (and memoizes) a `CApi` instance for that group
  name - this is the handle everything else hangs off.
- `createDispatcher()` returns a `CApi_Dispatcher`. `setPrefix()` is the URL
  prefix the group answers to. `setMethodNamespace()` is where incoming
  requests get resolved to a class: a request to `api/widget/resize/image`
  resolves to `Cresenity\Demo\Api\Widget\Method\Resize\Image`.
- Method namespaces can be either PSR-4 (leading `\`, joined with `\`, as
  above) or CF's classic underscore convention (e.g.
  `MyApp_Api_Widget_Method`, joined with `_`, resolving to
  `MyApp_Api_Widget_Method_Resize_Image`) - the dispatcher detects which one
  you're using from whether the namespace string starts with `\`.

Wire the dispatcher up to actually handle requests from a plain controller:

```php
class Controller_Api_Widget extends CController {
    public function __call($method, $args) {
        return \Cresenity\Demo\Api\Widget::instance()->dispatcher()->dispatch();
    }
}
```

---

### Method Classes

Every endpoint is one class extending `CApi_MethodAbstract` (or an
app-specific abstract that adds shared middleware - see below). At minimum,
implement `execute()` and set `$this->errCode` / `$this->errMessage` /
`$this->data`:

```php
// Cresenity/Demo/Api/Widget/Method/Resize/Image.php
namespace Cresenity\Demo\Api\Widget\Method\Resize;

use Cresenity\Demo\Api\Widget\MethodAbstract;

class Image extends MethodAbstract {
    public function execute() {
        $this->errCode = 0;
        $this->errMessage = '';
        $url = $this->getApiRequest()->url;
        $width = (int) $this->getApiRequest()->width;
        $this->data = [
            'url' => $url . '?resized_to=' . $width,
        ];
    }
}
```

Apps almost always add one shared `MethodAbstract` per group to register
common middleware once instead of per-method - see
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

The real `Image.php` also carries a full `@OA\Get(...)` annotation block
above the class - covered in [Generating Docs](/docs/api/docs-generation).
`MethodAbstract` here extends `CApi_OAuth_MethodAbstract` (not the plain
`CApi_MethodAbstract` shown earlier) because this example group uses OAuth -
see [OAuth2](/docs/api/oauth) for why and what
`Middleware\UserCredentialsMiddleware` does.

---

### Group Config

Config for a group is optional (sane defaults apply if the key is missing)
but lets you override error formatting, debug mode, and - if the group uses
OAuth - key paths:

```php
// default/config/api.php
return [
    'default' => 'widget',
    'groups' => [
        'widget' => [
            'error_format' => [
                'errCode' => ':code',
                'errMessage' => ':message',
                'data' => [],
            ],
            'debug' => !MyApp::isProduction(),
        ],
    ],
];
```

---

### Where to Go Next

- Need real user/client authentication instead of a shared public prefix?
  See [OAuth2 (CApi_OAuth)](/docs/api/oauth).
- Need interactive, importable API documentation? See
  [Generating Docs](/docs/api/docs-generation).
