# Configuration

Halaman ini menjelaskan konfigurasi dasar yang **wajib dipenuhi** sebelum menggunakan **Cresenity Framework (CF)**.

---

## Server Requirements

Pastikan server Anda telah memenuhi requirement berikut sebelum menjalankan Cresenity Framework.

### PHP
- PHP >= 7.4

### PHP Extensions

Extension PHP berikut **harus aktif**:

- BCMath
- JSON (sudah termasuk secara default sejak PHP 5.6)
- Fileinfo
- Ctype
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML

> **Catatan:**
> Sebagian besar extension di atas sudah aktif secara default pada instalasi PHP modern.
> Anda dapat mengeceknya dengan menjalankan perintah `php -m`.

---

## Application Folders

Setelah menginstall Cresenity Framework, Anda harus membuat folder aplikasi di dalam direktori `applications`.

---

## Directory Permission

Pastikan direktori berikut dapat ditulis (**writable**) oleh web server agar Cresenity Framework dapat berjalan dengan baik:

- `temp`
- `logs`
