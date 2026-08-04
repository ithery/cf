# PHPCF - Testing

## Test directories

- **Framework tests**: `tests/` in the framework root directory
- **Application tests**: `application/{appCode}/default/tests/` (Unit and Feature)

## How `phpcf test` resolves context

The `phpcf test` command detects its context from the current working directory:

| CWD | phpunit.xml | Tests directory |
|-----|------------|-----------------|
| Framework root (`htdocs/`) | `phpunit.xml` (root) | `tests/` |
| App directory (`htdocs/application/nwo/`) | `application/nwo/phpunit.xml` | `application/nwo/default/tests/` |

## Commands

### test:install

Installs the test dependencies — `phpunit.xml` and the `tests` folder — for an application.

```
cd application/myapp
phpcf test:install
```

### test

Runs the tests for the current working directory context.

```bash
# From the framework root — run the framework tests
cd htdocs/
phpcf test

# From an app directory — run that application's tests
cd htdocs/application/nwo/
phpcf test

# Disable TTY output
phpcf test --without-tty

# Run a specific file or folder
phpcf test Unit
phpcf test Unit/MyTest.php
```

### cf:test

Always runs the framework tests, using the root `phpunit.xml`, regardless of the working
directory.

```
phpcf cf:test
```

### test:chrome-driver

Installs the ChromeDriver binary used for browser testing.

```
phpcf test:chrome-driver {version?}
```
