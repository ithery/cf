# API - Generating Docs (Swagger/OpenAPI)

`CApi_Docs_GeneratorFactory` scans your Method classes for `@OA\...`
annotations (the same annotation syntax as `zircote/swagger-php`) and emits a
real OpenAPI JSON spec - which a Swagger UI page then renders with a working
"Try it out" button. Nothing here is hand-written JSON.

This page's example continues `Cresenity\Demo\Api\Widget` from
[Introduction](/docs/api/introduction) and [OAuth2](/docs/api/oauth) - see
`application/cresenity/default/libraries/Cresenity/Demo/Api/`.

---

### 1. Annotate Your Methods

The minimum viable annotation just registers the path:

```php
<?php
/**
 * @OA\PathItem(path="/api/widget/resize/image")
 */
class Image extends MethodAbstract {
    // ...
}
```

That's enough for the endpoint to show up in the generated spec and be
callable from Swagger UI, but with no parameters/response schema documented.
For a properly documented operation, annotate the HTTP method too - the full
version, from `Cresenity\Demo\Api\Widget\Method\Resize\Image`:

```php
// Cresenity/Demo/Api/Widget/Method/Resize/Image.php
/**
 * @OA\Get(
 *     path="/api/widget/resize/image",
 *     tags={"Widget"},
 *     summary="Resize an image",
 *     security={{"oauth2": {}}},
 *     @OA\Parameter(
 *         name="url",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", format="uri")
 *     ),
 *     @OA\Parameter(
 *         name="width",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Resized image URL",
 *         @OA\JsonContent(
 *             @OA\Property(property="errCode", type="integer"),
 *             @OA\Property(property="errMessage", type="string"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="url", type="string")
 *             )
 *         )
 *     )
 * )
 */
class Image extends MethodAbstract {
    // ...
}
```

The `security={{"oauth2": {}}}` line is what makes this specific operation
require the "Authorize" flow in Swagger UI - see the security scheme
registered in step 2.

Annotations can also live in dedicated "docs-only" classes/dirs that never
run - useful for shared schemas (`@OA\Schema`) or the top-level `@OA\Info`
block reused across multiple groups.

---

### 2. Wire the Generator

Put this on your group's main class, next to `dispatcher()` - see
`Cresenity\Demo\Api\Widget::generateDocs()`:

```php
// Cresenity/Demo/Api/Widget.php
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

protected function swaggerDir() {
    return \c::appRoot(['default', 'data', 'docs', 'api', 'widget', 'swagger']);
}

public function swaggerJsonPath() {
    return rtrim($this->swaggerDir(), DS) . DS . 'api-docs.json';
}
```

- `addAnnotationDir()` can be called multiple times - scan your `Method/`
  directory plus any shared `Docs/` annotation directory.
- `setConstants()` values are available inside annotations as
  `{WIDGET_API_TITLE}`-style placeholders if your annotations use them
  (matches l5-swagger's constants-replacement behavior).
- `setSecuritySchemes()` is what registers the OAuth "Authorize" button seen
  in [OAuth2](/docs/api/oauth) - omit it entirely for a group with no auth.
- The generated `api-docs.json` is normally **gitignored** and regenerated on
  demand (server-side via a controller action, or locally via `phpcf
  tinker`) - see the convention note at the end of this page.

---

### 3. Serve the Spec + Swagger UI

Two routes: one that returns the raw JSON (generating it on first access if
missing), one that renders the Swagger UI page pointed at it.

```php
<?php
class Controller_Docs_Api_Widget extends CController {
    public function swagger() {
        $path = \Cresenity\Demo\Api\Widget::instance()->swaggerJsonPath();
        if (!file_exists($path)) {
            \Cresenity\Demo\Api\Widget::instance()->generateDocs();
        }

        return c::response(file_get_contents($path), 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function runner() {
        if (D::isProduction()) {
            return c::abort(404);
        }

        \Cresenity\Demo\Api\Widget::instance()->generateDocs(); // always fresh in dev
        $app = c::app();
        $app->setView('docs');
        $app->addView('docs.swagger', [
            'openApiPath' => $this->controllerUrl() . 'swagger',
        ]);

        return $app;
    }

    public function generate() {
        \Cresenity\Demo\Api\Widget::instance()->generateDocs();

        return c::response()->json(['message' => 'Widget API docs regenerated']);
    }
}
```

The `docs.swagger` view boots `SwaggerUIBundle` against that URL:

```html
<div id="swagger-ui"></div>
<script>
    const ui = SwaggerUIBundle({
        dom_id: '#swagger-ui',
        url: "{!! $openApiPath !!}",
        oauth2RedirectUrl: "{{ $oauth2RedirectUrl }}",
        requestInterceptor: function(request) {
            request.headers['X-CSRF-TOKEN'] = '{{ c::csrfToken() }}';
            return request;
        },
        presets: [SwaggerUIBundle.presets.apis],
        deepLinking: true,
    });
    window.ui = ui;
</script>
```

The `requestInterceptor` is a good place to auto-inject an `Authorization`
header instead of relying on the manual "Authorize" dialog - e.g. a logged-in
developer's own API key for an in-dashboard playground.

Swagger UI's "Try it out" button on each operation works out of the box once
a spec is loaded - no extra flag needed (it's only disabled if you
explicitly set `tryItOutEnabled: false` or an empty
`supportedSubmitMethods`).

---

### Convention: Don't Commit Generated JSON

The generated `api-docs.json` is normally **gitignored** and regenerated
either lazily (first request to the `swagger` action, as above) or
explicitly via the `generate` action / a `phpcf tinker` call:

```bash
phpcf tinker --execute='Cresenity\Demo\Api\Widget::instance()->generateDocs();'
```

This keeps annotation edits from needing a separate "don't forget to
regenerate" step to be forgotten, and avoids noisy diffs on every method
change.

Also worth gating any "regenerate + render" runner action behind
`if (D::isProduction()) { return c::abort(404); }` (or your app's
equivalent), as shown above - it re-scans your whole annotation tree on
every hit, which is fine for local/dev use but not something you want
publicly reachable.
