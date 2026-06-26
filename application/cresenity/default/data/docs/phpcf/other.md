# PHPCF - Other

### API

| Command | Deskripsi |
|---------|-----------|
| `phpcf api:jwt-secret` | Generate JWTAuth secret key |
| `phpcf api:oauth:key` | Membuat encryption keys untuk API authentication |
| `phpcf api:oauth:client` | Membuat OAuth client |

### Asset

| Command | Deskripsi |
|---------|-----------|
| `phpcf asset:install` | Install asset ke public folder |
| `phpcf asset:google-fonts:fetch` | Download Google Fonts |

### Queue

| Command | Deskripsi |
|---------|-----------|
| `phpcf queue:clear` | Menghapus semua job dari queue |
| `phpcf queue:failed` | Menampilkan daftar failed jobs |
| `phpcf queue:prune-batches` | Membersihkan batch entries yang sudah lama |

### Translation

| Command | Deskripsi |
|---------|-----------|
| `phpcf translations:check` | Mengecek kelengkapan translations untuk semua bahasa |

### Documentation

| Command | Deskripsi |
|---------|-----------|
| `phpcf docs:phpdoc:install` | Install phpDocumentor |
| `phpcf docs:phpdoc:generate` | Generate source documentation |
| `phpcf docs:apigen:install` | Install ApiGen |
| `phpcf docs:apigen:generate` | Generate API documentation |

### WebSocket

| Command | Deskripsi |
|---------|-----------|
| `phpcf websocket:serve` | Menjalankan WebSocket server |

### Server Monitor

| Command | Deskripsi |
|---------|-----------|
| `phpcf server:monitor:listen {resources?}` | Monitor memory, CPU, network, dan nginx status |
