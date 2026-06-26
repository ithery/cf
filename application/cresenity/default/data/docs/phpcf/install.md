# PHPCF - Installation

### Install With Composer

PHPCF adalah command line tool untuk mengelola aplikasi Cresenity Framework. Install secara global melalui Composer:

```
composer global require cresenity/phpcf
```

Pastikan direktori global composer `vendor/bin` sudah ada di `PATH` sistem anda.

### Penggunaan

Setelah terinstall, jalankan `phpcf` dari root directory project:

```
phpcf list
```

### Multi Aplikasi

Karena CF bersifat multi aplikasi, command akan berjalan pada konteks satu domain/aplikasi. Cek domain aktif dengan:

```
phpcf status
```

Gunakan `domain:switch` untuk berpindah aplikasi:

```
phpcf domain:switch domain.name
```

### Daftar Command

Seluruh default command yang tersedia didefinisikan pada class `CFConsole` di `system/core/CFConsole.php`. Lihat menu di samping untuk daftar command per kategori.
