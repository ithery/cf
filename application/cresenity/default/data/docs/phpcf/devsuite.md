# PHPCF - DevSuite

### Install & Uninstall

```
phpcf devsuite:install
phpcf devsuite:uninstall
```

### Service

```
phpcf devsuite:start
phpcf devsuite:restart
phpcf devsuite:stop
```

### Link

Menghubungkan direktori project ke DevSuite.

```
phpcf devsuite:link
phpcf devsuite:links
phpcf devsuite:unlink
```

### SSL/TLS

Mengamankan domain dengan trusted TLS certificate.

```
phpcf devsuite:secure
phpcf devsuite:unsecure
```

### TLD

Mengatur Top Level Domain untuk DevSuite.

```
phpcf devsuite:tld
```

### Open & Share

```
phpcf devsuite:open
phpcf devsuite:share
```

### SSH

```
phpcf devsuite:ssh
phpcf devsuite:ssh:list
phpcf devsuite:ssh:create
```

### Deploy

```
phpcf devsuite:deploy:init
phpcf devsuite:deploy:run
```

### Database

```
phpcf devsuite:db:install
phpcf devsuite:db:start
phpcf devsuite:db:uninstall
```

| Command | Deskripsi |
|---------|-----------|
| `phpcf devsuite:db:list` | Menampilkan daftar database |
| `phpcf devsuite:db:create` | Membuat database baru |
| `phpcf devsuite:db:delete` | Menghapus database |
| `phpcf devsuite:db:compare` | Membandingkan database |
| `phpcf devsuite:db:clone` | Clone database |
| `phpcf devsuite:db:sync` | Sinkronisasi database |
