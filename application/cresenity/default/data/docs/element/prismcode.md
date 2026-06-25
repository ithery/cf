# Element - Prism Code

The `CElement_Component_PrismCode` component renders syntax-highlighted code blocks using Prism.js. It supports multiple languages, themes, copy-to-clipboard, and code selection.

Create a code block:

```php
$app = c::app();
$code = CElement_Component_PrismCode::factory();
$code->setLanguage('php');
$code->add(htmlspecialchars('<?php echo "Hello, World!"; ?>'));
$app->add($code);

return $app;
```

---

### Language

Set the programming language for syntax highlighting:

```php
$code->setLanguage('php');
$code->setLanguage('javascript');
$code->setLanguage('html');
$code->setLanguage('css');
$code->setLanguage('sql');
$code->setLanguage('json');
$code->setLanguage('bash');
```

---

### Theme

Set the Prism.js color theme:

```php
$code->setTheme('okaidia');     // default, dark theme
$code->setTheme('tomorrow');
$code->setTheme('coy');
$code->setTheme('twilight');
```

---

### Copy to Clipboard

Add a "Copy" button to the code block:

```php
$code->setHaveCopyToClipboard(true);
```

---

### Select Code

Add a "Select Code" button:

```php
$code->setHaveSelectCode(true);
```

---

### Word Wrap

Enable word wrapping for long lines:

```php
$code->setWrap(true);
```

---

### Full Example

```php
$code = CElement_Component_PrismCode::factory();
$code->setLanguage('php');
$code->setTheme('okaidia');
$code->setHaveCopyToClipboard(true);
$code->setWrap(true);
$code->add(htmlspecialchars($sourceCode));
$app->add($code);
```

> **Note:** Always use `htmlspecialchars()` when adding code content to prevent HTML injection.
