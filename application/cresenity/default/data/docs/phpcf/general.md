# PHPCF - General

### version

Displays the CF version currently in use.

```
phpcf version
```

### about

Displays complete information about the application.

```
phpcf about
```

To display a single section only:

```
phpcf about --only=environment
```

### environment

Displays the active framework environment.

```
phpcf environment
```

### serve

Runs the application on the PHP built-in development server.

```
phpcf serve
```

### key:generate

Generates a new application key.

```
phpcf key:generate
```

### composer

Runs a composer command through phpcf.

```
phpcf composer {args}
```

### npm

Runs an npm command through phpcf.

```
phpcf npm {npmArgs}
```
