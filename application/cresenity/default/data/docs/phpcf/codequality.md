# PHPCF - Code Quality

### PHPStan

```
phpcf phpstan {path?} --format=table --debug --no-progress
phpcf phpstan:install
phpcf phpstan:clear
```

| Command | Description |
|---------|-------------|
| `phpcf phpstan {path?}` | Run PHPStan static analysis |
| `phpcf phpstan:install` | Install PHPStan |
| `phpcf phpstan:clear` | Clear the PHPStan cache |

### PHP CodeSniffer

```
phpcf phpcs {path?} --format=table --debug --no-progress
phpcf phpcs:fix {path?}
phpcf phpcs:install
phpcf phpcs:config
```

| Command | Description |
|---------|-------------|
| `phpcf phpcs {path?}` | Run PHP CodeSniffer |
| `phpcf phpcs:fix {path?}` | Fix PHPCS violations |
| `phpcf phpcs:install` | Install PHPCS |
| `phpcf phpcs:config` | Configure PHPCS |

### PHP CS Fixer

```
phpcf php-cs-fixer {path?}
phpcf php-cs-fixer:format {path}
phpcf php-cs-fixer:install
phpcf php-cs-fixer:config
```

| Command | Description |
|---------|-------------|
| `phpcf php-cs-fixer {path?}` | Run PHP CS Fixer |
| `phpcf php-cs-fixer:format {path}` | Format code |
| `phpcf php-cs-fixer:install` | Install PHP CS Fixer |
| `phpcf php-cs-fixer:config` | Configure PHP CS Fixer |
