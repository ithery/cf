# Application - Introduction

### Overview

One of the key features of Cresenity Framework is the `CApp` object — a page builder that serves as the core of page construction. `CApp` handles the full page lifecycle: theme loading, asset management, navigation, content rendering, and response generation.

The `CApp` instance is a singleton and can be accessed using `c::app()`:

```php
<?php
class Controller_Home extends CController {
    public function index() {
        $app = c::app();

        return $app;
    }
}
```

When you return `$app` from a controller, the framework automatically renders the complete HTML page using the configured theme, registered assets, navigation, and content.

### What CApp Handles

CApp manages several aspects of a page:

- **Theme** — determines the HTML layout, CSS, and JavaScript
- **Title** — the page title shown in the browser tab
- **Navigation** — sidebar or top navigation menus
- **Content** — UI elements (tables, forms, widgets, views) added programmatically or via Blade views
- **Assets** — CSS and JavaScript files registered at runtime
- **Authentication** — login requirements and auth guards
- **SEO** — meta tags and Open Graph data

### Basic Usage

A typical controller using CApp:

```php
<?php
class Controller_App_Dashboard extends CController {
    public function index() {
        $app = c::app();
        $app->title('Dashboard');
        $app->setLoginRequired(true);
        $app->setNav('main');

        $app->addView('dashboard', [
            'stats' => $this->getStats(),
        ]);

        return $app;
    }
}
```

### CApp vs Other Response Types

| Approach | Use Case |
|----------|----------|
| `return c::app()` | Full HTML pages with theme, navigation, and assets |
| `return c::view()` | Standalone Blade views without CApp wrapper |
| `return c::response()->json()` | API endpoints returning JSON |
| `return c::response()` | Plain text or custom responses |
| `return c::redirect()` | Redirects |

For most web pages in a CF application, `CApp` is the recommended approach. Use other response types for APIs, redirects, or pages that need a completely custom layout.
