# CF Command - Basic

### Output

Beberapa method tersedia untuk menampilkan output pada console:

```php
// Informasi (hijau)
$this->info('Proses selesai');

// Error (merah)
$this->error('Terjadi kesalahan');

// Warning (kuning)
$this->warn('Perhatian');

// Teks biasa
$this->line('Teks biasa');

// Tabel
$this->table(['Header 1', 'Header 2'], [
    ['Row 1 Col 1', 'Row 1 Col 2'],
    ['Row 2 Col 1', 'Row 2 Col 2'],
]);
```

### Input Interaktif

```php
// Pertanyaan
$name = $this->ask('Siapa nama anda?');

// Pertanyaan dengan default
$name = $this->ask('Siapa nama anda?', 'Default');

// Konfirmasi
if ($this->confirm('Apakah anda yakin?')) {
    // ...
}

// Pilihan
$option = $this->choice('Pilih warna', ['merah', 'biru', 'hijau']);
```

### Mengakses Argument dan Option

```php
public function handle() {
    // Mendapatkan satu argument
    $user = $this->argument('user');

    // Mendapatkan semua arguments
    $arguments = $this->arguments();

    // Mendapatkan satu option
    $queue = $this->option('queue');

    // Mendapatkan semua options
    $options = $this->options();
}
```

### Command Berbasis Aplikasi

Untuk command yang memerlukan konteks aplikasi (database, config, dll), extend `CConsole_Command_AppCommand`:

```php
class CConsole_Command_MyAppCommand extends CConsole_Command_AppCommand {
    protected $signature = 'my:app-command';

    protected $description = 'Command yang memerlukan konteks aplikasi';

    public function handle() {
        // Akses database, config, dll sudah tersedia
        $db = c::db();
    }
}
```

### Memanggil Command dari Code

```php
// Dari controller atau code lain
CConsole::call('my:command', ['argument' => 'value', '--option' => 'value']);
```
