# CF Command - Introduction

### Membuat Command

CF menyediakan fitur untuk membuat command console custom pada aplikasi. Command dibuat dengan meng-extend class `CConsole_Command`.

### Struktur Command

Sebuah command class memiliki dua property utama dan satu method yang harus diimplementasikan:

```php
class CConsole_Command_MyCommand extends CConsole_Command {
    protected $signature = 'my:command {argument} {--option=}';

    protected $description = 'Deskripsi command';

    public function handle() {
        $argument = $this->argument('argument');
        $option = $this->option('option');

        $this->info('Command berhasil dijalankan');
    }
}
```

### Signature

Property `$signature` mendefinisikan nama command beserta argument dan option yang diterima:

```php
// Argument wajib
protected $signature = 'mail:send {user}';

// Argument opsional
protected $signature = 'mail:send {user?}';

// Argument dengan default value
protected $signature = 'mail:send {user=foo}';

// Option dengan value
protected $signature = 'mail:send {--queue=}';

// Option boolean (flag)
protected $signature = 'mail:send {--queue}';

// Variadic argument
protected $signature = 'mail:send {user*}';
```

### Registrasi Command

Semua default command didefinisikan pada `CFConsole` di `system/core/CFConsole.php` dalam property `$defaultCommands`.

Untuk mendaftarkan command custom pada aplikasi, gunakan method `addCommand`:

```php
CFConsole::addCommand(MyCustomCommand::class);
```

Atau beberapa command sekaligus:

```php
CFConsole::addCommand([
    MyCommand1::class,
    MyCommand2::class,
]);
```
