# Application - Authentication
### Configuration

Konfigurasi auth terletak pada file `auth.php`

Pada konfigurasi auth, ada 2 element penting yang perlu diperhatikan adalah guard dan provider.

### Database

Secara default CApp sudah menyertakan model `CApp_Model_User` yang akan digunakan pada model user untuk proses authentikasi.

Ketika membuat table users baru, yang perlu diperhatikan adalah harus ada kolom `password` minimal 60 karakter, dan kolom `remember_token` dengan minimal 100 karakter.

Kolom `remember_token` akan digunakan untuk menyimpan token yang diperoleh saat user memilih "remember me" option saat user login pada aplikasi
