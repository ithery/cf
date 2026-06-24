# VSCode Setup

Visual Studio Code is the recommended editor for Cresenity Framework development. The framework ships with workspace configuration files and has a dedicated extension for enhanced IDE support.

---

### PHP CF Extension

Install the official Cresenity Framework extension from the VS Code Marketplace:

**[PHP CF](https://marketplace.visualstudio.com/items?itemName=cresenity.php-cf)**

This extension provides:

- Class name autocompletion for CF's underscore-to-directory naming convention (e.g. `CApp_Base` resolves to `system/libraries/CApp/Base.php`)
- Go-to-definition support for CF classes
- Code snippets for common CF patterns

#### Installation

1. Open VS Code
2. Press `Ctrl+Shift+X` (or `Cmd+Shift+X` on macOS) to open the Extensions panel
3. Search for **"PHP CF"**
4. Click **Install**

Or install via the command line:

```bash
code --install-extension cresenity.php-cf
```

---

### Recommended Extensions

The following extensions work well with the configuration files included in the framework:

| Extension | Purpose |
|-----------|---------|
| [PHP CS Fixer](https://marketplace.visualstudio.com/items?itemName=junstyle.php-cs-fixer) | Automatic code formatting using `.php-cs-fixer.dist.php` |
| [PHPStan](https://marketplace.visualstudio.com/items?itemName=swordev.phpstan) | Static analysis using `phpstan.neon` |
| [PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client) | PHP language server for autocompletion and diagnostics |
| [EditorConfig](https://marketplace.visualstudio.com/items?itemName=EditorConfig.EditorConfig) | Consistent formatting using `.editorconfig` |

---

### Included Configuration Files

The framework root includes several configuration files that VS Code and its extensions will automatically pick up:

#### .php-cs-fixer.dist.php

Defines the code style rules for the project. When the PHP CS Fixer extension is installed, it will automatically use this file to format your PHP code on save.

Key rules enforced:

- Short array syntax (`[]` instead of `array()`)
- Single quotes for strings
- Opening braces on the same line for classes and functions
- Ordered imports by length
- PHPDoc alignment and formatting

#### .editorconfig

Ensures consistent editor settings across all contributors:

- UTF-8 charset
- LF line endings
- 4-space indentation
- Trailing whitespace trimming

#### phpstan.neon

Configuration for PHPStan static analysis. Run analysis from the command line:

```bash
./system/vendor/PHPStan/phpstan analyse
```

#### phpcs.xml

Code sniffer rules based on the PEAR standard with CF-specific customizations (same-line braces, relaxed commenting rules).

---

### IDE Helper

The framework includes a `_ide_helper.php` file in the root directory. This file provides additional type hints for classes that use dynamic methods (such as `CApp`), improving autocompletion in VS Code and other IDEs.

This file is loaded automatically by Intelephense and similar language servers — no additional configuration is required.

---

### Workspace Snippets

The framework includes VS Code snippets in `.vscode/cf.json.code-snippets`. Currently available:

| Prefix | Snippet |
|--------|---------|
| `phpstan-ignore-next-line` | Adds a `@phpstan-ignore-next-line` comment |

You can add your own project-specific snippets to this file.
