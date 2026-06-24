# Directory Structure

### Introduction

Cresenity Framework supports multiple applications running within a single framework installation. Each application lives in its own subdirectory under `application/` and is identified by an `app_code` defined in the domain configuration.

The root directory structure is fixed and should not be modified. You are free to organize files within your application's subdirectories as needed.

---

### Root Directory Structure

```
/                           (Document Root)
├── application/            Application directory (one subfolder per app)
├── config/                 Shared framework configuration
├── data/                   Runtime data
│   ├── daemon/             Daemon state files
│   └── domain/             Domain-to-app mapping files
├── logs/                   Log files (must be writable)
├── media/                  Shared static assets
│   ├── css/                CSS assets
│   └── js/                 JavaScript assets
├── modules/                Optional framework modules
├── public/                 Public assets (fonts, etc.)
├── resources/              Resources (must be writable)
├── system/                 Framework core (do not modify)
│   ├── core/               Core classes (CF, CFRouter, etc.)
│   ├── libraries/          Framework libraries
│   └── vendor/             Third-party dependencies
├── temp/                   Temporary files (must be writable)
├── tests/                  Framework-level tests
├── cf                      CLI entry point (phpcf)
└── index.php               Web entry point
```

> **Note:** The `logs/`, `resources/`, and `temp/` directories must be writable by the web server.

---

### Application Directory Structure

Each application follows the structure `application/{app_code}/`. Inside, files are organized by organization code (`org_code`). The `default/` directory serves as the base and is always loaded. If an `org_code` is configured, its directory is loaded on top of `default/`, allowing per-organization overrides.

```
application/{app_code}/
├── default/                Base application files (always loaded)
│   ├── config/             Application configuration
│   ├── controllers/        Controller classes
│   ├── data/               Application data files
│   ├── i18n/               Translation files
│   ├── libraries/          Application-specific libraries
│   ├── media/              Application static assets
│   │   ├── css/            CSS files
│   │   └── js/             JavaScript files
│   ├── navs/               Navigation definitions
│   ├── tests/              Application tests
│   ├── themes/             Theme files
│   └── views/              View templates
├── {org_code}/             Organization-specific overrides (optional)
│   ├── controllers/        Override or extend controllers
│   ├── config/             Override configuration
│   ├── views/              Override views
│   └── ...                 (same structure as default/)
├── bootstrap.php           Application bootstrap file
└── env.php                 Environment variables
```

---

### File Lookup Order

When the framework resolves a file (controller, view, config, etc.), it searches in the following order:

1. `application/{app_code}/{org_code}/` — organization-specific override
2. `application/{app_code}/default/` — application base
3. `system/` — framework defaults

The first match wins. This allows organizations to override specific files without duplicating the entire application.

---

### Domain Configuration

Each domain is mapped to an application via a PHP file in `data/domain/`. The filename must match the domain:

**File:** `data/domain/myproject.dev.cresenity.com.php`

```php
<?php
return [
    'app_code' => 'myproject',
    'app_id'   => '1',
    'org_code' => 'myproject',
    'org_id'   => 1,
];
```

For local development, you can also use the `.test` TLD shortcut. A domain like `myproject.test` will automatically resolve to `app_code = 'myproject'` without needing a domain configuration file.
