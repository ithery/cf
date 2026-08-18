# PHPCF - Other

### API

| Command | Description |
|---------|-----------|
| `phpcf api:jwt-secret` | Generate JWTAuth secret key |
| `phpcf api:oauth:key` | Create encryption keys for API authentication |
| `phpcf api:oauth:client` | Create an OAuth client |

### Asset

| Command | Description |
|---------|-----------|
| `phpcf asset:install` | Install assets into the public folder |
| `phpcf asset:google-fonts:fetch` | Download Google Fonts |

### Queue

| Command | Description |
|---------|-----------|
| `phpcf queue:clear` | Delete every job from the queue |
| `phpcf queue:failed` | List the failed jobs |
| `phpcf queue:prune-batches` | Prune stale batch entries |

### Translation

| Command | Description |
|---------|-----------|
| `phpcf translations:check` | Check translation completeness across every language |

### Documentation

| Command | Description |
|---------|-----------|
| `phpcf docs:phpdoc:install` | Install phpDocumentor |
| `phpcf docs:phpdoc:generate` | Generate source documentation |
| `phpcf docs:apigen:install` | Install ApiGen |
| `phpcf docs:apigen:generate` | Generate API documentation |

### WebSocket

| Command | Description |
|---------|-----------|
| `phpcf websocket:serve` | Run the WebSocket server |

### Server Monitor

| Command | Description |
|---------|-----------|
| `phpcf server:monitor:listen {resources?}` | Monitor memory, CPU, network, and nginx status |
