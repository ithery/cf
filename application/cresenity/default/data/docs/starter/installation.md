# Installation


1. Setting `SSH Key`
    - Generate SSH Key

        [Machintosh](https://docs.github.com/en/authentication/connecting-to-github-with-ssh/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent?platform=mac)

        [Windows](https://docs.github.com/en/authentication/connecting-to-github-with-ssh/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent?platform=windows)

        [Linux](https://docs.github.com/en/authentication/connecting-to-github-with-ssh/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent?platform=linux)

        *setelah public key berhasil dibuat, buka dengan text editor kemudian copy*

    - Add SSH Key

        Login ke [https://git.ittron.co.id/](https://git.ittron.co.id/)

        Navigasikan ke `account preference` kemudian pilih menu `SSH Keys`, lanjutkan dengan paste public key ke textarea

        ![add ssh key to git server](http://dev.ittron.co.id/application/cresenity/default/media/img/docs/starter/add-ssh-key.jpg)


2. Clone `Cresenity Framework`

        git clone git@git.ittron.co.id:root/CApp.git

    *setelah melakukan clone git, ganti nama file index.php.sample menjadi index.php*


3. Install `phpcf extension`.

    [phpcf](/docs/phpcf/install)


4. Clone Project

    clone project yang di handle di folder `application` melalui ssh.
    contoh misalnya project dengan nama projectsatu, maka struktur direktori menjadi `application/projectsatu`


5. Project Baru **Khusus untuk pembuatan project baru*

    1. run command

            phpcf app:create nama_project

        contoh: `phpcf app:create projectdua` (enter lalu buat kode projek yang belum digunakan)

    2. upload folder `projectdua` untuk remote ke subdomain ittron

    3. set up domain di folder data/domain (jika tidak ada folder data buat folder lalu download)

    4. buat file `projectdua.dev.ittron.co.id.php` di folder data/domain

    5. isi file copy paste dengan yang sebelumnya dan sesuaikan

    6. run web dengan di address bar `projectdua.dev.ittron.co.id`
