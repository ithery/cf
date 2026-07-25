# PHPCF - Application

### init

Wizard interaktif untuk membuat aplikasi baru. Harus dijalankan dari dalam folder aplikasinya sendiri (`application/{code}/`) — kode aplikasi otomatis diambil dari nama folder tersebut, tidak perlu diinput. Wizard akan menanyakan prefix class aplikasi (dipakai untuk generate base library, mis. `OH.php` untuk app dengan prefix `OH`), dan opsional preset admin, lalu membuat scaffolding project-nya.

```
cd application/propmind
phpcf init
```

Opsi berikut boleh diisi untuk melewati pertanyaan wizard yang bersangkutan:

```
phpcf init --domain= --prefix= --title= --admin
```

- `--domain` — domain lokal untuk aplikasi ini, default `{code}.test`
- `--prefix` — prefix class untuk base library aplikasi (mis. `PM`), tidak boleh `CF`
- `--title` — judul aplikasi, default nama kode aplikasi
- `--admin` — jika diberikan, langsung scaffold preset admin tanpa ditanya

### app:code

Menampilkan atau mengatur app code.

```
phpcf app:code {appCode?}
```
