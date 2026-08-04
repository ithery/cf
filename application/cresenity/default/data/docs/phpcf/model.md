# PHPCF - Model

Command pada halaman ini membaca model beserta tabelnya. Seluruhnya dijalankan dari dalam
folder aplikasi, karena model yang dibaca adalah milik aplikasi tersebut.

```
cd application/ohayomart
phpcf model:list
```

### model:list

Menampilkan seluruh model yang terdaftar di aplikasi.

```
phpcf model:list
```

### model:show

Menampilkan rincian sebuah model: nama tabel, koneksi, kolom beserta tipenya, dan relasi yang
dideklarasikan.

```
phpcf model:show {model}
```

Nama model boleh ditulis tanpa prefix aplikasinya:

```
phpcf model:show Product
phpcf model:show OHModel_Product
```

Opsi:

- `--database=` — koneksi yang dipakai, bila modelnya tidak memakai koneksi bawaan
- `--json` — keluarkan sebagai JSON alih-alih tabel, untuk diproses skrip lain

```
phpcf model:show Product --json
```

Relasi yang dikenali: `hasOne`, `hasMany`, `hasOneThrough`, `hasManyThrough`, `belongsTo`,
`belongsToMany`, `morphOne`, `morphTo`, `morphMany`, `morphToMany`, dan `morphedByMany`.

### model:tables

Menampilkan tabel yang memiliki model. Berguna untuk menemukan tabel yang **belum** punya
model — bandingkan keluarannya dengan daftar tabel dari command `database`.

```
phpcf model:tables
```

### model:update

Memperbarui anotasi properti pada berkas model agar sesuai dengan kolom tabelnya. Dipakai
setelah skema berubah, sehingga docblock `@property` kembali mencerminkan basis data dan
analisis statis maupun pelengkapan otomatis IDE tetap benar.

```
phpcf model:update {table}
```

```
phpcf model:update product
```

Yang diubah hanya blok anotasi; kode di dalam kelasnya tidak disentuh.

## Scout

Tersedia bila aplikasi memakai pengindeksan pencarian.

### model:scout:flush

Mengosongkan seluruh record sebuah model dari indeks pencarian.

```
phpcf model:scout:flush {model}
```

### model:scout:delete-all-indexes

Menghapus seluruh indeks.

```
phpcf model:scout:delete-all-indexes
```
