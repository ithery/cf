# PHPCF - Commands

Daftar lengkap command yang tersedia. Semua command didefinisikan pada class `CFConsole` di `system/core/CFConsole.php`.

### General

| Command | Deskripsi |
|---------|-----------|
| `phpcf version` | Menampilkan versi CF |
| `phpcf about` | Menampilkan informasi aplikasi |
| `phpcf environment` | Menampilkan environment framework |
| `phpcf serve` | Menjalankan aplikasi pada PHP development server |
| `phpcf tinker` | Interaksi langsung dengan aplikasi (REPL) |
| `phpcf key:generate` | Generate application key |
| `phpcf composer` | Menjalankan composer command |
| `phpcf npm` | Menjalankan npm command |

### Domain

| Command | Deskripsi |
|---------|-----------|
| `phpcf domain:list` | Menampilkan daftar semua domain |
| `phpcf domain:create` | Membuat domain baru di `data/domain` |
| `phpcf domain:delete {domain}` | Menghapus domain |
| `phpcf domain:switch {domain}` | Berpindah ke domain lain |

### Application

| Command | Deskripsi |
|---------|-----------|
| `phpcf app:create {code}` | Membuat aplikasi baru (`--domain=`, `--prefix=`, `--title=`) |
| `phpcf app:preset` | Mengatur preset aplikasi |
| `phpcf app:preset:admin` | Mengatur preset admin |
| `phpcf app:code {appCode?}` | Menampilkan atau mengatur app code |

### Make (Scaffolding)

| Command | Deskripsi |
|---------|-----------|
| `phpcf make:controller {controller}` | Membuat controller baru |
| `phpcf make:model {table}` | Membuat model class berdasarkan tabel database |
| `phpcf make:config {config}` | Membuat file config baru (`--value=`) |
| `phpcf make:nav {nav}` | Membuat file navigasi baru |
| `phpcf make:theme {theme}` | Membuat theme baru |
| `phpcf make:test` | Membuat test class baru |

### Model

| Command | Deskripsi |
|---------|-----------|
| `phpcf model:list` | Menampilkan daftar model |
| `phpcf model:show {model}` | Menampilkan informasi detail model |
| `phpcf model:tables` | Menampilkan daftar tabel model |
| `phpcf model:update {table}` | Update properties model |

### Database

| Command | Deskripsi |
|---------|-----------|
| `phpcf db {connection?}` | Membuka session CLI database |
| `phpcf db:explain` | Explain dan analisa query |
| `phpcf db:monitor` | Monitor jumlah koneksi database |
| `phpcf db:show` | Menampilkan informasi database (`--database=`) |
| `phpcf db:schema {table}` | Menampilkan schema tabel |

### Cron / Schedule

| Command | Deskripsi |
|---------|-----------|
| `phpcf cron:list` | Menampilkan daftar scheduled commands (`--timezone=`) |
| `phpcf cron:run` | Menjalankan scheduled commands |
| `phpcf cron:work` | Menjalankan schedule worker |
| `phpcf cron:test` | Menjalankan satu scheduled command untuk testing |
| `phpcf cron:finish {id} {code}` | Handle penyelesaian scheduled command |

### Queue

| Command | Deskripsi |
|---------|-----------|
| `phpcf queue:clear` | Menghapus semua job dari queue |
| `phpcf queue:failed` | Menampilkan daftar failed jobs |
| `phpcf queue:prune-batches` | Membersihkan batch entries yang sudah lama |

### Daemon

| Command | Deskripsi |
|---------|-----------|
| `phpcf daemon:list` | Menampilkan daftar daemon |
| `phpcf daemon:start {class}` | Menjalankan daemon |
| `phpcf daemon:status {class}` | Mengecek status daemon |
| `phpcf daemon:stop {class}` | Menghentikan daemon |
| `phpcf daemon:supervisor:start` | Menjalankan supervisor baru |
| `phpcf daemon:supervisor:status` | Mengecek status supervisor |

### Testing

| Command | Deskripsi |
|---------|-----------|
| `phpcf test:install` | Install test dependencies |
| `phpcf test` | Menjalankan application tests (`--without-tty`) |
| `phpcf test:chrome-driver {version?}` | Install ChromeDriver binary |

### Code Quality

| Command | Deskripsi |
|---------|-----------|
| `phpcf phpstan {path?}` | Menjalankan PHPStan analysis |
| `phpcf phpstan:install` | Install PHPStan |
| `phpcf phpstan:clear` | Membersihkan PHPStan cache |
| `phpcf phpcs {path?}` | Menjalankan PHP CodeSniffer |
| `phpcf phpcs:install` | Install PHPCS |
| `phpcf phpcs:config` | Konfigurasi PHPCS |
| `phpcf phpcs:fix {path?}` | Fix PHPCS violations |
| `phpcf php-cs-fixer {path?}` | Menjalankan PHP CS Fixer |
| `phpcf php-cs-fixer:install` | Install PHP CS Fixer |
| `phpcf php-cs-fixer:format {path}` | Format code |
| `phpcf php-cs-fixer:config` | Konfigurasi PHP CS Fixer |

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

### Translation

| Command | Deskripsi |
|---------|-----------|
| `phpcf translations:check` | Mengecek kelengkapan translations untuk semua bahasa |

### Documentation

| Command | Deskripsi |
|---------|-----------|
| `phpcf docs:phpdoc:install` | Install phpDocumentor |
| `phpcf docs:phpdoc:generate` | Generate source documentation (`--output`) |
| `phpcf docs:apigen:install` | Install ApiGen |
| `phpcf docs:apigen:generate` | Generate API documentation (`--output`) |

### WebSocket

| Command | Deskripsi |
|---------|-----------|
| `phpcf websocket:serve` | Menjalankan WebSocket server |

### Server Monitor

| Command | Deskripsi |
|---------|-----------|
| `phpcf server:monitor:listen {resources?}` | Monitor memory, CPU, network, dan nginx status |

### DevSuite

| Command | Deskripsi |
|---------|-----------|
| `phpcf devsuite:install` | Install DevSuite |
| `phpcf devsuite:uninstall` | Uninstall DevSuite |
| `phpcf devsuite:start` | Start DevSuite services |
| `phpcf devsuite:restart` | Restart DevSuite services |
| `phpcf devsuite:stop` | Stop DevSuite services |
| `phpcf devsuite:link` | Link direktori ke DevSuite |
| `phpcf devsuite:links` | Menampilkan daftar semua links |
| `phpcf devsuite:unlink` | Menghapus link |
| `phpcf devsuite:secure` | Mengamankan domain dengan TLS certificate |
| `phpcf devsuite:unsecure` | Menghapus TLS dari domain |
| `phpcf devsuite:tld` | Mengatur TLD DevSuite |
| `phpcf devsuite:open` | Membuka site di browser |
| `phpcf devsuite:share` | Share site secara publik |
| `phpcf devsuite:ssh` | Koneksi SSH |
| `phpcf devsuite:ssh:list` | Menampilkan daftar SSH |
| `phpcf devsuite:ssh:create` | Membuat SSH baru |
| `phpcf devsuite:deploy:init` | Membuat file deployment baru |
| `phpcf devsuite:deploy:run` | Menjalankan deployment |
| `phpcf devsuite:db:install` | Install database service |
| `phpcf devsuite:db:start` | Start database service |
| `phpcf devsuite:db:uninstall` | Uninstall database service |
| `phpcf devsuite:db:list` | Menampilkan daftar database |
| `phpcf devsuite:db:create` | Membuat database baru |
| `phpcf devsuite:db:delete` | Menghapus database |
| `phpcf devsuite:db:compare` | Membandingkan database |
| `phpcf devsuite:db:clone` | Clone database |
| `phpcf devsuite:db:sync` | Sinkronisasi database |
