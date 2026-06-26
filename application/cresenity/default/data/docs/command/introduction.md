# CF Command - Introduction

### Command Call

CF mempunyai command console yang sudah terinclude dalam framework. CF Command tersedia pada root directory aplikasi kita dan terdapat banyak command yang membantu kita dalam pembuatan aplikasi.

Untuk melihat seluruh command yang tersedia, kita dapat menggunakan `list` command:

```
php cf list
```

### Registrasi Command

Seluruh default command yang tersedia didefinisikan pada class `CFConsole` di `system/core/CFConsole.php`. Property `$defaultCommands` berisi daftar semua command class yang selalu tersedia.

Untuk menambahkan command custom pada aplikasi, gunakan method `addCommand`:

```php
CFConsole::addCommand(MyCustomCommand::class);
```

Atau untuk menambahkan beberapa command sekaligus:

```php
CFConsole::addCommand([
    MyCommand1::class,
    MyCommand2::class,
]);
```

### Multi Aplikasi

Karena CF bersifat multi aplikasi, command akan berjalan pada konteks satu domain/aplikasi. Gunakan `domain:switch` untuk berpindah aplikasi sebelum menjalankan command.
