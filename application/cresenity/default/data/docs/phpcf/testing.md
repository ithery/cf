# PHPCF - Testing

## Test Directories

- **Framework tests**: `tests/` di root directory framework
- **Application tests**: `application/{appCode}/default/tests/` (Unit dan Feature)

## Perilaku `phpcf test` berdasarkan CWD

Command `phpcf test` secara otomatis mendeteksi context berdasarkan current working directory:

| CWD | phpunit.xml | Tests Directory |
|-----|------------|-----------------|
| Framework root (`htdocs/`) | `phpunit.xml` (root) | `tests/` |
| App directory (`htdocs/application/nwo/`) | `application/nwo/phpunit.xml` | `application/nwo/default/tests/` |

## Commands

### test:install

Install test dependencies (phpunit.xml dan folder tests) untuk aplikasi.

```
cd application/myapp
phpcf test:install
```

### test

Menjalankan tests sesuai context CWD.

```bash
# Dari framework root — jalankan framework tests
cd htdocs/
phpcf test

# Dari app directory — jalankan app tests
cd htdocs/application/nwo/
phpcf test

# Disable TTY output
phpcf test --without-tty

# Jalankan test file/folder tertentu
phpcf test Unit
phpcf test Unit/MyTest.php
```

### cf:test

Selalu menjalankan framework tests (dari root phpunit.xml), terlepas dari CWD.

```
phpcf cf:test
```

### test:chrome-driver

Install ChromeDriver binary untuk browser testing.

```
phpcf test:chrome-driver {version?}
```
