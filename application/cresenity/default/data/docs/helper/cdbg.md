# Helper cdbg

The `cdbg` helper class provides debugging output: dumping values, reading call traces, and
inspecting the last executed query.

## Dumping values

### cdbg::d

The `cdbg::d` method dumps one or more values and continues execution:

```php
cdbg::d($user);

cdbg::d($request, $response, $config);
```

### cdbg::dd

The `cdbg::dd` method dumps one or more values and terminates the script:

```php
cdbg::dd($user);
```

Both methods delegate to `CDebug::dumper()`, so the output follows the configured dumper:
collapsible and syntax-highlighted in the browser, plain text on the command line.

### cdbg::varDump

The `cdbg::varDump` method produces a simple dump that does not depend on the framework
dumper. Passing `true` as the second argument returns the output instead of printing it:

```php
cdbg::varDump($data);

$html = cdbg::varDump($data, true);
```

### cdbg::varDumpPlain

The `cdbg::varDumpPlain` method returns the highlighted HTML representation of a value
without a wrapper:

```php
$html = cdbg::varDumpPlain($data);
```

### cdbg::dump

The `cdbg::dump` method is the underlying formatter, with configurable string length and
recursion depth:

```php
$html = cdbg::dump($value, 128, 10);
```

The recursion limit prevents infinite output when dumping structures that reference each
other, such as related models.

## Call traces

### cdbg::trace

The `cdbg::trace` method returns a formatted backtrace with shortened file paths and
arguments removed:

```php
$trace = cdbg::trace();
```

### cdbg::getTraceString

The `cdbg::getTraceString` method returns the trace as a string:

```php
$text = cdbg::getTraceString();
```

### cdbg::traceDump

The `cdbg::traceDump` method prints the trace, or returns it when passed `true`:

```php
cdbg::traceDump();

$html = cdbg::traceDump(true);
```

### cdbg::callerInfo

The `cdbg::callerInfo` method returns a description of the calling location one level above
the current one:

```php
$caller = cdbg::callerInfo();

// APPPATH/controllers/home.php:42 Controller_Home::index()
```

### cdbg::callerInfoArray

The `cdbg::callerInfoArray` method returns the same information as an array:

```php
$caller = cdbg::callerInfoArray();

// ['file' => ..., 'line' => ..., 'class' => ..., 'function' => ...]
```

### cdbg::varDumpTrace

The `cdbg::varDumpTrace` method prints a message followed by the current backtrace:

```php
cdbg::varDumpTrace('reached the fallback branch');
```

## Database

### cdbg::dumpLastQuery

The `cdbg::dumpLastQuery` method displays the most recently executed query:

```php
cdbg::dumpLastQuery();

cdbg::dumpLastQuery($db);
```

### cdbg::queryDump

The `cdbg::queryDump` method returns the query dump when passed `true` as the second
argument:

```php
$html = cdbg::queryDump(null, true);
```

## Paths and source

### cdbg::path

The `cdbg::path` method shortens an absolute path to its symbolic form:

```php
cdbg::path('/var/www/html/system/libraries/CApp.php');

// SYSPATH/libraries/CApp.php
```

The recognised prefixes are `APPPATH`, `SYSPATH`, `MODPATH`, and `DOCROOT`.

### cdbg::source

The `cdbg::source` method returns the lines surrounding a given line in a file, which is how
error pages display the offending code:

```php
$lines = cdbg::source($file, $line, 5);
```

### cdbg::deprecated

The `cdbg::deprecated` method records the use of a deprecated API with the debug collector:

```php
cdbg::deprecated('cfoo::bar() is deprecated, use CFoo::bar()');
```

## Production use

`cdbg::d` and `cdbg::dd` write directly to the output stream. Calls left in production code
will corrupt JSON responses and, in the case of `cdbg::dd`, terminate the request. Remove
them before committing.
