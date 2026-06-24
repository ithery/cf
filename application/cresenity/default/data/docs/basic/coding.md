# Coding Standard & Autoloading

## Coding Standard

### Overview

- Files MUST use only `<?php` tags.
- Files MUST use only UTF-8 without BOM for PHP code.
- Files SHOULD *either* declare symbols (classes, functions, constants, etc.) *or* cause side-effects (e.g. generate output, change .ini settings, etc.) but SHOULD NOT do both.
- Class names MUST be declared in `StudlyCaps` with a category prefix (e.g. `CApp`, `CModel`, `CRouting`).
- Class constants MUST be declared in all upper case with underscore separators.
- Method names MUST be declared in `camelCase`.
- Code MUST use 4 spaces for indenting, not tabs.
- Visibility MUST be declared on all properties and methods; `abstract` and `final` MUST be declared before the visibility; `static` MUST be declared after the visibility.
- Control structure keywords MUST have one space after them; method and function calls MUST NOT.
- Opening braces for classes, methods, and control structures MUST go on the **same line**.
- Closing braces MUST go on the next line after the body.

### Files

#### PHP Tags

PHP code MUST use the long `<?php ?>` tags or the short-echo `<?= ?>` tags. It MUST NOT use other tag variations.

- All PHP files MUST use Unix LF (linefeed) line endings.
- All PHP files MUST end with a single blank line.
- The closing `?>` tag MUST be omitted from files containing only PHP.

#### Character Encoding

PHP code MUST use only UTF-8 without BOM.

#### Side Effects

A file SHOULD declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it SHOULD execute logic with side effects, but SHOULD NOT do both.

"Side effects" include but are not limited to: generating output, explicit use of `require` or `include`, connecting to external services, modifying ini settings, emitting errors or exceptions, modifying global or static variables, reading from or writing to a file, and so on.

Example of what to **avoid** (declarations mixed with side effects):

```php
<?php
ini_set('error_reporting', E_ALL);
include 'file.php';
echo "<html>\n";

function foo() {
    // function body
}
```

Example of what to **follow** (declarations only):

```php
<?php
function foo() {
    // function body
}

if (!function_exists('bar')) {
    function bar() {
        // function body
    }
}
```

#### Indenting

Code MUST use an indent of 4 spaces, and MUST NOT use tabs for indenting.

#### Keywords and True/False/Null

PHP keywords MUST be in lower case.

The PHP constants `true`, `false`, and `null` MUST be in lower case.

### Class and File Naming

Cresenity Framework does **not** use PHP namespaces for its core libraries. Instead, it uses an underscore-based naming convention where the underscore character (`_`) maps to a directory separator.

#### Library Classes

Library classes start with an uppercase letter and are loaded from `system/libraries/` (or `application/{app_code}/default/libraries/`).

| Class Name | File Path |
|---|---|
| `CApp` | `libraries/CApp.php` |
| `CApp_Base` | `libraries/CApp/Base.php` |
| `CModel_Collection` | `libraries/CModel/Collection.php` |
| `CRouting_RouteFinder` | `libraries/CRouting/RouteFinder.php` |
| `CVendor_Firebase` | `libraries/CVendor/Firebase.php` |

Each segment after an underscore becomes a directory, except for the last segment which becomes the filename.

#### Helper Classes

Helper classes start with a lowercase letter and are loaded from `system/helpers/` (or `application/{app_code}/default/helpers/`).

| Class Name | File Path |
|---|---|
| `c` | `helpers/c.php` |
| `carr` | `helpers/carr.php` |
| `cstr` | `helpers/cstr.php` |
| `curl` | `helpers/curl.php` |

#### Controller Classes

Controller classes use the `Controller_` prefix and are loaded from the `controllers/` directory. The underscore-to-directory mapping also applies after the prefix.

| Class Name | File Path |
|---|---|
| `Controller_Home` | `controllers/home.php` |
| `Controller_App` | `controllers/app.php` |
| `Controller_App_Home` | `controllers/app/home.php` |
| `Controller_Admin_Setting_Plan` | `controllers/admin/setting/plan.php` |

> **Note:** Controller filenames are lowercase.

#### Third-party Classes

Third-party libraries that use PHP namespaces (e.g. Symfony, Illuminate) are loaded from `system/vendor/` using their own autoloading conventions.

### Properties and Constants

#### Constants

Class constants MUST be declared in all upper case with underscore separators.

```php
<?php
class CApp_Config {
    const VERSION = '1.8';
    const DATE_FORMAT = 'Y-m-d';
}
```

#### Use Declarations

When present, all `use` declarations MUST go at the top of the file, after the opening `<?php` tag and any `defined('SYSPATH')` guard.

There MUST be one `use` keyword per declaration, and one blank line after the `use` block.

```php
<?php
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\HttpFoundation\Response;

class CModel extends CModel_Base implements Arrayable {
    // ...
}
```

#### Properties

Visibility MUST be declared on all properties.

The `var` keyword MUST NOT be used to declare a property.

There MUST NOT be more than one property declared per statement.

Property names SHOULD NOT be prefixed with a single underscore to indicate protected or private visibility.

```php
<?php
class CApp_Base {
    public $title = '';

    protected $theme = 'default';

    private $initialized = false;
}
```

### Methods

Method names MUST be declared in `camelCase`.

Visibility MUST be declared on all methods.

The opening brace MUST go on the **same line** as the method declaration. There MUST NOT be a space after the opening parenthesis, and there MUST NOT be a space before the closing parenthesis.

```php
<?php
class CApp_Base {
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    protected function resolveTheme() {
        // method body
    }
}
```

#### Method Arguments

In the argument list, there MUST NOT be a space before each comma, and there MUST be one space after each comma.

Arguments with default values MUST go at the end of the argument list.

Argument lists MAY be split across multiple lines. When doing so, the first item MUST be on the next line, and there MUST be only one argument per line.

```php
<?php
class CElement_Component_DataTable {
    public function setAjax(
        $url,
        $method = 'GET',
        array $params = []
    ) {
        // method body
    }
}
```

#### Extends and Implements

The `extends` and `implements` keywords MUST be declared on the same line as the class name. The opening brace MUST go on the same line.

```php
<?php
class CApp_Base implements CApp_Contract_BaseInterface {
    // constants, properties, methods
}
```

Lists of `implements` MAY be split across multiple lines, with one interface per line:

```php
<?php
use Countable;
use ArrayAccess;
use Serializable;

class CCollection extends CCollection_Base implements
    ArrayAccess,
    Countable,
    Serializable {
    // constants, properties, methods
}
```

#### abstract, final, and static

When present, `abstract` and `final` MUST precede the visibility declaration. `static` MUST come after the visibility declaration.

```php
<?php
abstract class CDatabase_Grammar {
    protected static $tablePrefix;

    abstract protected function compileSelect(CDatabase_Query $query);

    final public static function getDateFormat() {
        // method body
    }
}
```

#### Method and Function Calls

There MUST NOT be a space between the method or function name and the opening parenthesis. In the argument list, there MUST NOT be a space before each comma, and there MUST be one space after each comma.

```php
<?php
$app = CApp::instance();
$app->setTitle('My App');
$users = CModel::factory('user')->get();
```

Argument lists MAY be split across multiple lines:

```php
<?php
$table->setAjax(
    curl::base() . 'api/users',
    'POST',
    ['page' => 1]
);
```

### Control Structures

The general style rules for control structures are as follows:

- There MUST be one space after the control structure keyword
- There MUST NOT be a space after the opening parenthesis
- There MUST NOT be a space before the closing parenthesis
- There MUST be one space between the closing parenthesis and the opening brace
- The structure body MUST be indented once
- The closing brace MUST be on the next line after the body
- The body MUST be enclosed by braces, even for single-line statements

#### if, elseif, else

```php
<?php
if ($expr1) {
    // if body
} elseif ($expr2) {
    // elseif body
} else {
    // else body
}
```

The keyword `elseif` SHOULD be used instead of `else if` so that all control keywords look like single words.

#### switch, case

The `case` statement MUST be indented once from `switch`. The `break` keyword MUST be indented at the same level as the `case` body. There MUST be a comment such as `// no break` when fall-through is intentional in a non-empty case body.

```php
<?php
switch ($expr) {
    case 0:
        echo 'First case, with a break';
        break;
    case 1:
        echo 'Second case, which falls through';
        // no break
    case 2:
    case 3:
        echo 'Third case, return instead of break';
        return;
    default:
        echo 'Default case';
        break;
}
```

#### Loops

```php
<?php
while ($expr) {
    // structure body
}

do {
    // structure body
} while ($expr);

for ($i = 0; $i < 10; $i++) {
    // for body
}

foreach ($iterable as $key => $value) {
    // foreach body
}
```

#### try, catch

```php
<?php
try {
    // try body
} catch (FirstExceptionType $e) {
    // catch body
} catch (OtherExceptionType $e) {
    // catch body
}
```

### Closures

Closures MUST be declared with a space after the `function` keyword, and a space before and after the `use` keyword.

The opening brace MUST go on the same line, and the closing brace MUST go on the next line following the body.

```php
<?php
$closureWithArgs = function ($arg1, $arg2) {
    // body
};

$closureWithArgsAndVars = function ($arg1, $arg2) use ($var1, $var2) {
    // body
};
```

Argument lists and variable lists MAY be split across multiple lines:

```php
<?php
$longArgs_longVars = function (
    $longArgument,
    $longerArgument,
    $muchLongerArgument
) use (
    $longVar1,
    $longerVar2,
    $muchLongerVar3
) {
    // body
};
```

The formatting rules also apply when the closure is used directly as a function argument:

```php
<?php
$foo->bar(
    $arg1,
    function ($arg2) use ($var1) {
        // body
    },
    $arg3
);
```

---

## Autoloading

### How It Works

The autoloader is registered in `CF::setup()` via `spl_autoload_register`. When a class is referenced, the autoloader resolves the file path based on the class name:

1. **First character is uppercase** → search in `libraries/` directory
2. **First character is lowercase** → search in `helpers/` directory
3. **Class starts with `Controller_`** → search in `controllers/` directory

In all cases, underscores (`_`) in the class name are converted to directory separators (`/`).

### File Search Order

The autoloader searches multiple paths in priority order:

1. `application/{app_code}/{org_code}/` — organization override
2. `application/{app_code}/default/` — application base
3. `system/` — framework core

The first matching file is loaded.

### Examples

```
CApp_Base                → system/libraries/CApp/Base.php
CDatabase_Query_Builder  → system/libraries/CDatabase/Query/Builder.php
Controller_App_Home      → application/{app}/default/controllers/app/home.php
carr                     → system/helpers/carr.php
```

### Third-party Libraries

Classes with PHP namespaces (using `\`) are resolved from the `system/vendor/` directory following their own directory conventions. For example, `Symfony\Component\HttpFoundation\Response` loads from `system/vendor/Symfony/Component/HttpFoundation/Response.php`.
