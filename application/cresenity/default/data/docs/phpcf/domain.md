# PHPCF - Domain

Karena CF bersifat multi aplikasi, command domain digunakan untuk mengelola dan berpindah antar domain/aplikasi.

### domain:list

Menampilkan daftar semua domain yang terdaftar.

```
phpcf domain:list
```

### domain:create

Membuat domain baru di `data/domain`.

```
phpcf domain:create
```

### domain:delete

Menghapus domain yang terdaftar.

```
phpcf domain:delete {domain}
```

### domain:switch

Berpindah ke domain lain. Semua command selanjutnya akan berjalan pada konteks domain yang dipilih.

```
phpcf domain:switch {domain}
```
