# Installation


1. Clone `Cresenity Framework`

        git clone git@github.com:cresenity/cf.git

    *setelah melakukan clone git, ganti nama file index.php.sample menjadi index.php*


2. Install `phpcf extension`.

    [phpcf](/docs/phpcf/install)


3. Clone Project

    clone project yang di handle di folder `application` melalui ssh.
    contoh misalnya project dengan nama myproject, maka struktur direktori menjadi `application/myproject`


4. Project Baru **Khusus untuk pembuatan project baru*

    1. run command

            phpcf app:create nama_project

        contoh: `phpcf app:create mysecondproject` (enter lalu buat kode projek yang belum digunakan)

    2. upload folder `mysecondproject` untuk remote ke subdomain ittron

    3. set up domain di folder data/domain (jika tidak ada folder data buat folder lalu download)

    4. buat file `mysecondproject.dev.cresenity.com.php` di folder data/domain

    5. isi file copy paste dengan yang sebelumnya dan sesuaikan

    6. run web dengan di address bar `mysecondproject.dev.cresenity.com`
