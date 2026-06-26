# PHPCF - Code Quality

### PHPStan

```
phpcf phpstan {path?} --format=table --debug --no-progress
phpcf phpstan:install
phpcf phpstan:clear
```

| Command | Deskripsi |
|---------|-----------|
| `phpcf phpstan {path?}` | Menjalankan PHPStan static analysis |
| `phpcf phpstan:install` | Install PHPStan |
| `phpcf phpstan:clear` | Membersihkan PHPStan cache |

### PHP CodeSniffer

```
phpcf phpcs {path?} --format=table --debug --no-progress
phpcf phpcs:fix {path?}
phpcf phpcs:install
phpcf phpcs:config
```

| Command | Deskripsi |
|---------|-----------|
| `phpcf phpcs {path?}` | Menjalankan PHP CodeSniffer |
| `phpcf phpcs:fix {path?}` | Fix PHPCS violations |
| `phpcf phpcs:install` | Install PHPCS |
| `phpcf phpcs:config` | Konfigurasi PHPCS |

### PHP CS Fixer

```
phpcf php-cs-fixer {path?}
phpcf php-cs-fixer:format {path}
phpcf php-cs-fixer:install
phpcf php-cs-fixer:config
```

| Command | Deskripsi |
|---------|-----------|
| `phpcf php-cs-fixer {path?}` | Menjalankan PHP CS Fixer |
| `phpcf php-cs-fixer:format {path}` | Format code |
| `phpcf php-cs-fixer:install` | Install PHP CS Fixer |
| `phpcf php-cs-fixer:config` | Konfigurasi PHP CS Fixer |
