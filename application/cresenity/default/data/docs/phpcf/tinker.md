# PHPCF - Tinker

### tinker

REPL interaktif terhadap aplikasi yang sedang berjalan — model, koneksi basis data,
konfigurasi, dan seluruh helper tersedia apa adanya. Dibangun di atas PsySH.

```
phpcf tinker
```

**Jalankan dari dalam folder aplikasinya.** Kode aplikasi diambil dari nama folder lewat
`CF::appCode()`, dan dari situ `bootstrap.php` milik aplikasi tersebut ikut dimuat saat boot.
Tidak perlu memanggil bootstrap secara manual.

```
cd application/ohayomart
phpcf tinker
```

Dijalankan dari root framework, yang terbuka adalah konteks framework — tanpa model maupun
konfigurasi aplikasi.

### Menjalankan kode langsung

Opsi `--execute` menjalankan satu potong kode lalu keluar. Berguna untuk skrip dan
pemeriksaan cepat:

```
phpcf tinker --execute='echo CF::appCode();'
```

### Memuat berkas lebih dulu

Argumen posisional memuat satu berkas atau lebih sebelum sesi dimulai:

```
phpcf tinker helper.php fixture.php
```

### Menjalankan skrip panjang

Pengutipan bersarang mudah rusak, terutama bila perintahnya melewati `ssh` atau `su -c`.
Untuk apa pun yang lebih dari satu baris, tulis ke berkas lalu panggil dengan `require`:

```
phpcf tinker --execute='require "/tmp/periksa.php";'
```

Cara ini juga membuat skripnya dapat dijalankan ulang tanpa menyusun ulang kutipannya.

### Menjelajah data tanpa meninggalkan jejak

Sesi tinker menyentuh basis data yang sesungguhnya. Bungkus apa pun yang menulis dalam
transaksi yang dibatalkan, sehingga perubahannya berlaku selama pemeriksaan lalu hilang:

```php
$db = c::db();
$db->begin();

$model = OHModel_Product::find(1);
$model->price = 5000;
$model->save();

// periksa akibatnya di sini

$db->rollback();
```

Ini cara paling andal memastikan sebuah perbaikan atau fixture benar-benar berperilaku sesuai
dugaan terhadap data nyata sebelum ditulis menjadi test.

### Keluaran yang diringkas

Beberapa jenis nilai ditampilkan dalam bentuk ringkas alih-alih seluruh isi objeknya:

| Kelas | Yang ditampilkan |
|---|---|
| `CModel` | atribut, atribut yang berubah, dan relasi yang sudah dimuat |
| `CCollection` | isi koleksinya |
| `CBase_HtmlString` | string HTML-nya |
| `CBase_String` | nilai stringnya |

Tambahan dapat didaftarkan lewat konfigurasi `tinker.casters`:

```php
// config/tinker.php
return [
    'casters' => [
        'OHModel_Order' => 'OHTinker_Caster::castOrder',
    ],
];
```

### Batasan konteks CLI

Tinker berjalan tanpa permintaan HTTP, dan sebagian bagian framework bergantung padanya:

- `CF::domain()` bernilai `{appCode}.test`, sehingga `CF::getFile('navs', ...)` dapat meleset
  dan `CNavigation_Data::get()` mengembalikan objek alih-alih larik nav;
- perender nav memerlukan data rute (`getRouteData()`), jadi keluaran sidenav tidak dapat
  diuji dari sini — periksa di browser;
- `$_SERVER['REQUEST_URI']` tidak ada, sehingga kode yang membacanya tanpa penjaga akan fatal.

### Konteks organisasi

Aplikasi yang bertenant memerlukan organisasi aktif, yang di HTTP ditentukan dari domain. Di
CLI ia harus dipasang sendiri — nama helpernya berbeda tiap aplikasi:

```php
OH::setOrgIdResolver(function () {
    return 12;
});
```

### Ekstensi PHP

Tinker memerlukan ekstensi `phar`. Bila `php` bawaan PATH tidak memilikinya, panggil biner
yang lengkap secara eksplisit:

```
/usr/local/lsws/lsphp84/bin/php $(which phpcf) tinker
```
