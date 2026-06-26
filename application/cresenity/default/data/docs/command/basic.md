# CF Command - Basic

### General

| Command | Deskripsi |
|---------|-----------|
| `php cf version` | Menampilkan versi CF |
| `php cf about` | Menampilkan informasi aplikasi |
| `php cf status` | Menampilkan domain dan aplikasi aktif |
| `php cf environment` | Menampilkan environment framework |
| `php cf serve` | Menjalankan aplikasi pada PHP development server |
| `php cf tinker` | Interaksi langsung dengan aplikasi (REPL) |
| `php cf key:generate` | Generate application key |
| `php cf composer` | Menjalankan composer command |
| `php cf npm` | Menjalankan npm command |

### Domain

| Command | Deskripsi |
|---------|-----------|
| `php cf domain:list` | Menampilkan daftar semua domain |
| `php cf domain:create` | Membuat domain baru di `data/domain` |
| `php cf domain:delete {domain}` | Menghapus domain |
| `php cf domain:switch {domain}` | Berpindah ke domain lain |

### Application

| Command | Deskripsi |
|---------|-----------|
| `php cf app:create {code}` | Membuat aplikasi baru (`--domain=`, `--prefix=`, `--title=`) |
| `php cf app:preset` | Mengatur preset aplikasi |
| `php cf app:preset:admin` | Mengatur preset admin |
| `php cf app:code {appCode?}` | Menampilkan atau mengatur app code |

### Make (Scaffolding)

| Command | Deskripsi |
|---------|-----------|
| `php cf make:controller {controller}` | Membuat controller baru |
| `php cf make:model {table}` | Membuat model class berdasarkan tabel database |
| `php cf make:config {config}` | Membuat file config baru (`--value=`) |
| `php cf make:nav {nav}` | Membuat file navigasi baru |
| `php cf make:theme {theme}` | Membuat theme baru |
| `php cf make:test` | Membuat test class baru |

### Model

| Command | Deskripsi |
|---------|-----------|
| `php cf model:list` | Menampilkan daftar model |
| `php cf model:show {model}` | Menampilkan informasi detail model |
| `php cf model:tables` | Menampilkan daftar tabel model |
| `php cf model:update {table}` | Update properties model |

### Database

| Command | Deskripsi |
|---------|-----------|
| `php cf db {connection?}` | Membuka session CLI database |
| `php cf db:explain` | Explain dan analisa query |
| `php cf db:monitor` | Monitor jumlah koneksi database |
| `php cf db:show` | Menampilkan informasi database (`--database=`) |
| `php cf db:schema {table}` | Menampilkan schema tabel |

### Cron / Schedule

| Command | Deskripsi |
|---------|-----------|
| `php cf cron:list` | Menampilkan daftar scheduled commands (`--timezone=`) |
| `php cf cron:run` | Menjalankan scheduled commands |
| `php cf cron:work` | Menjalankan schedule worker |
| `php cf cron:test` | Menjalankan satu scheduled command untuk testing |
| `php cf cron:finish {id} {code}` | Handle penyelesaian scheduled command |

### Queue

| Command | Deskripsi |
|---------|-----------|
| `php cf queue:clear` | Menghapus semua job dari queue |
| `php cf queue:failed` | Menampilkan daftar failed jobs |
| `php cf queue:prune-batches` | Membersihkan batch entries yang sudah lama |

### Daemon

| Command | Deskripsi |
|---------|-----------|
| `php cf daemon:list` | Menampilkan daftar daemon |
| `php cf daemon:start {class}` | Menjalankan daemon |
| `php cf daemon:status {class}` | Mengecek status daemon |
| `php cf daemon:stop {class}` | Menghentikan daemon |
| `php cf daemon:supervisor:start` | Menjalankan supervisor baru |
| `php cf daemon:supervisor:status` | Mengecek status supervisor |

### Testing

| Command | Deskripsi |
|---------|-----------|
| `php cf test:install` | Install test dependencies |
| `php cf test` | Menjalankan application tests (`--without-tty`) |
| `php cf test:chrome-driver {version?}` | Install ChromeDriver binary |

### Code Quality

| Command | Deskripsi |
|---------|-----------|
| `php cf phpstan {path?}` | Menjalankan PHPStan analysis |
| `php cf phpstan:install` | Install PHPStan |
| `php cf phpstan:clear` | Membersihkan PHPStan cache |
| `php cf phpcs {path?}` | Menjalankan PHP CodeSniffer |
| `php cf phpcs:install` | Install PHPCS |
| `php cf phpcs:config` | Konfigurasi PHPCS |
| `php cf phpcs:fix {path?}` | Fix PHPCS violations |
| `php cf php-cs-fixer {path?}` | Menjalankan PHP CS Fixer |
| `php cf php-cs-fixer:install` | Install PHP CS Fixer |
| `php cf php-cs-fixer:format {path}` | Format code |
| `php cf php-cs-fixer:config` | Konfigurasi PHP CS Fixer |

### API

| Command | Deskripsi |
|---------|-----------|
| `php cf api:jwt-secret` | Generate JWTAuth secret key |
| `php cf api:oauth:key` | Membuat encryption keys untuk API authentication |
| `php cf api:oauth:client` | Membuat OAuth client |

### Asset

| Command | Deskripsi |
|---------|-----------|
| `php cf asset:install` | Install asset ke public folder |
| `php cf asset:google-fonts:fetch` | Download Google Fonts |

### Translation

| Command | Deskripsi |
|---------|-----------|
| `php cf translations:check` | Mengecek kelengkapan translations untuk semua bahasa |

### Documentation

| Command | Deskripsi |
|---------|-----------|
| `php cf docs:phpdoc:install` | Install phpDocumentor |
| `php cf docs:phpdoc:generate` | Generate source documentation (`--output`) |
| `php cf docs:apigen:install` | Install ApiGen |
| `php cf docs:apigen:generate` | Generate API documentation (`--output`) |

### WebSocket

| Command | Deskripsi |
|---------|-----------|
| `php cf websocket:serve` | Menjalankan WebSocket server |

### Server Monitor

| Command | Deskripsi |
|---------|-----------|
| `php cf server:monitor:listen {resources?}` | Monitor memory, CPU, network, dan nginx status |

### DevSuite

| Command | Deskripsi |
|---------|-----------|
| `php cf devsuite:install` | Install DevSuite |
| `php cf devsuite:uninstall` | Uninstall DevSuite |
| `php cf devsuite:start` | Start DevSuite services |
| `php cf devsuite:restart` | Restart DevSuite services |
| `php cf devsuite:stop` | Stop DevSuite services |
| `php cf devsuite:link` | Link direktori ke DevSuite |
| `php cf devsuite:links` | Menampilkan daftar semua links |
| `php cf devsuite:unlink` | Menghapus link |
| `php cf devsuite:secure` | Mengamankan domain dengan TLS certificate |
| `php cf devsuite:unsecure` | Menghapus TLS dari domain |
| `php cf devsuite:tld` | Mengatur TLD DevSuite |
| `php cf devsuite:open` | Membuka site di browser |
| `php cf devsuite:share` | Share site secara publik |
| `php cf devsuite:ssh` | Koneksi SSH |
| `php cf devsuite:ssh:list` | Menampilkan daftar SSH |
| `php cf devsuite:ssh:create` | Membuat SSH baru |
| `php cf devsuite:deploy:init` | Membuat file deployment baru |
| `php cf devsuite:deploy:run` | Menjalankan deployment |
| `php cf devsuite:db:install` | Install database service |
| `php cf devsuite:db:start` | Start database service |
| `php cf devsuite:db:uninstall` | Uninstall database service |
| `php cf devsuite:db:list` | Menampilkan daftar database |
| `php cf devsuite:db:create` | Membuat database baru |
| `php cf devsuite:db:delete` | Menghapus database |
| `php cf devsuite:db:compare` | Membandingkan database |
| `php cf devsuite:db:clone` | Clone database |
| `php cf devsuite:db:sync` | Sinkronisasi database |
