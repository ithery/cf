# PHPCF - Domain

Karena CF bersifat multi aplikasi, domain adalah yang menentukan aplikasi mana yang dilayani
sebuah permintaan. Command pada halaman ini mengelola pendaftaran domain dan berpindah
antar-domain saat bekerja di CLI.

## Bentuk pendaftarannya

Tiap domain adalah satu berkas PHP di `data/domain/{domain}.php` yang mengembalikan larik:

```php
<?php

return [
    'app_id' => '2100',
    'app_code' => '3hweb',
    'org_id' => null,
    'org_code' => null,
];
```

`app_code` inilah yang dibaca `CF::appCode()` saat menangani permintaan HTTP, sehingga domain
menentukan aplikasi. `org_id` dan `org_code` mengikat domain ke satu organisasi pada aplikasi
bertenant, dan dibiarkan kosong bila organisasinya ditentukan dengan cara lain.

### domain:list

Menampilkan seluruh domain yang terdaftar.

```
phpcf domain:list
```

### domain:create

Mendaftarkan domain baru dengan menulis berkasnya ke `data/domain`.

```
phpcf domain:create {domain}
```

Opsi:

- `--appId=` — `app_id` untuk domain ini, bawaannya `1`
- `--appCode=` — `app_code` untuk domain ini, bawaannya `cresenity`
- `--orgId=` — `org_id`, bila domain terikat satu organisasi
- `--orgCode=` — `org_code`, bila domain terikat satu organisasi

```
phpcf domain:create ohayomart.test --appId=2100 --appCode=ohayomart
```

### domain:delete

Menghapus pendaftaran sebuah domain.

```
phpcf domain:delete {domain}
```

Yang dihapus hanya berkas pendaftarannya; berkas aplikasinya tidak disentuh.

### domain:switch

Menetapkan domain aktif untuk pekerjaan di CLI. Command sesudahnya berjalan pada konteks
domain tersebut.

```
phpcf domain:switch {domain}
```

Pilihannya disimpan di `data/current-domain` dan **bertahan lintas sesi** — ia berlaku sampai
diganti, bukan hanya selama terminal terbuka. Command menolak berpindah ke domain yang belum
terdaftar, dan memberi tahu bila kamu sudah berada di domain itu.

## Hubungannya dengan folder kerja

Ada dua cara konteks aplikasi ditentukan di CLI, dan keduanya perlu diketahui karena tidak
selalu sepakat:

| Cara | Dipakai oleh |
|---|---|
| folder kerja — `application/{code}/` | `phpcf tinker`, `phpcf test` |
| domain aktif — `data/current-domain` | command yang berjalan dari root framework |

Bila sebuah command tampak menjalankan aplikasi yang salah, periksa keduanya: berada di
folder aplikasi yang benar, dan `domain:switch` menunjuk ke domain yang benar.
