# Helper chtml

The `chtml` helper class builds HTML tags and escapes values for safe output.

### chtml::specialchars

The `chtml::specialchars` method converts special characters to HTML entities. Pass `false`
as the second argument to leave existing entities untouched:

```php
$safe = chtml::specialchars('<b>Tom & Jerry</b>');

// &lt;b&gt;Tom &amp; Jerry&lt;/b&gt;

$safe = chtml::specialchars($value, false);
```

### chtml::attributes

The `chtml::attributes` method compiles an array into an attribute string. Values are escaped
with `chtml::specialchars`:

```php
$attributes = chtml::attributes(['class' => 'btn', 'id' => 'save']);

// ' class="btn" id="save"'
```

The result begins with a space so it can be concatenated directly after a tag name. A string
argument is returned as-is, prefixed with a space.

### chtml::anchor

The `chtml::anchor` method builds an anchor tag. The URI is resolved against the site base
URL unless it already contains a protocol:

```php
$link = chtml::anchor('user/profile', 'Profile');

// <a href="http://example.com/user/profile">Profile</a>

$link = chtml::anchor('user/profile', 'Profile', ['class' => 'nav-link']);
```

When the title is omitted, the URI is used as the link text. Pass `true` as the fifth
argument to escape the title.

### chtml::file_anchor

The `chtml::file_anchor` method builds an anchor pointing at a file relative to the document
root rather than the site base URL:

```php
$link = chtml::file_anchor('media/report.pdf', 'Download report');
```

### chtml::panchor

The `chtml::panchor` method builds an anchor with an explicit protocol:

```php
$link = chtml::panchor('https', 'example.com/page', 'Open');
```

### chtml::anchor_array

The `chtml::anchor_array` method builds an anchor from an array of arguments in the same order
as `chtml::anchor`:

```php
$link = chtml::anchor_array(['user/profile', 'Profile']);
```

### chtml::email

The `chtml::email` method obfuscates an email address using HTML entities, so it is harder to
harvest from the page source:

```php
$obfuscated = chtml::email('someone@example.com');
```

### chtml::mailto

The `chtml::mailto` method builds an obfuscated `mailto:` anchor:

```php
$link = chtml::mailto('someone@example.com', 'Contact us');
```

### chtml::meta

The `chtml::meta` method builds one or more meta tags. Passing an array produces one tag per
entry:

```php
$meta = chtml::meta('description', 'Page description');

$meta = chtml::meta([
    'description' => 'Page description',
    'keywords' => 'cresenity, framework',
]);
```

### chtml::stylesheet

The `chtml::stylesheet` method builds a stylesheet link tag:

```php
$tag = chtml::stylesheet('media/css/app.css');

$tag = chtml::stylesheet('media/css/print.css', 'print');
```

An array of paths produces one tag per entry.

### chtml::script

The `chtml::script` method builds a script tag:

```php
$tag = chtml::script('media/js/app.js');
```

### chtml::link

The `chtml::link` method builds a generic link tag, and is the method the stylesheet helper
delegates to:

```php
$tag = chtml::link('media/favicon.ico', 'shortcut icon', 'image/x-icon');
```

### chtml::image

The `chtml::image` method builds an image tag. The `src` is converted to an absolute URL
unless it already contains a protocol:

```php
$tag = chtml::image('media/img/logo.png', 'Logo');

// <img src="http://example.com/media/img/logo.png" alt="Logo" />
```

Passing an array as the first argument supplies the full attribute set:

```php
$tag = chtml::image(['src' => 'media/img/logo.png', 'alt' => 'Logo', 'width' => 120]);
```

## Index files

Several methods accept a final `$index` argument. When `true`, the generated URL includes the
front controller (`index.php`) segment. It defaults to `false`.

```php
chtml::script('media/js/app.js', true);
```
