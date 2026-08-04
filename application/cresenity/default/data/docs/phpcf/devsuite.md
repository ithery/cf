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

Links a project directory into DevSuite.

```
phpcf devsuite:link
phpcf devsuite:links
phpcf devsuite:unlink
```

### SSL/TLS

Secures a domain with a trusted TLS certificate.

```
phpcf devsuite:secure
phpcf devsuite:unsecure
```

### TLD

Sets the top level domain used by DevSuite.

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

### Database

```
phpcf devsuite:db:install
phpcf devsuite:db:start
phpcf devsuite:db:uninstall
```

| Command | Description |
|---------|-----------|
| `phpcf devsuite:db:list` | List the databases |
| `phpcf devsuite:db:create` | Create a new database |
| `phpcf devsuite:db:delete` | Delete a database |
| `phpcf devsuite:db:compare` | Compare databases |
| `phpcf devsuite:db:clone` | Clone a database |
| `phpcf devsuite:db:sync` | Synchronise databases |
