# Application - Authentication
### Configuration

Konfigurasi auth terletak pada file `auth.php`

Pada konfigurasi auth, ada 2 element penting yang perlu diperhatikan adalah guard dan provider.

### Database

Secara default CApp sudah menyertakan model `CApp_Model_User` yang akan digunakan pada model user untuk proses authentikasi.

Ketika membuat table users baru, yang perlu diperhatikan adalah harus ada kolom `password` minimal 60 karakter, dan kolom `remember_token` dengan minimal 100 karakter.

Kolom `remember_token` akan digunakan untuk menyimpan token yang diperoleh saat user memilih "remember me" option saat user login pada aplikasi


### Login

Contoh Kode:

```php
public function login() {
    $post = c::request()->post();
    if (!empty($post)) {
        $email = isset($post['email']) ? $post['email'] : '';
        $password = isset($post['password']) ? $post['password'] : '';
        $rememberMe = isset($post['remember-me']) ? true : false;

        $errCode = 0;
        $errMessage = '';
        $json = [];

        if ($errCode == 0) {
            if (strlen($email) == 0) {
                $errCode++;
                $errMessage = 'Email required';
            }
        }
        if ($errCode == 0) {
            if (strlen($password) == 0) {
                $errCode++;
                $errMessage = 'Password required';
            }
        }

        if ($errCode == 0) {
            try {
                $successLogin = c::app()->auth()->attempt(['username' => $email, 'password' => $password], $rememberMe);
                if ($successLogin) {
                    cmsg::clear('error');
                } else {
                    $errCode++;
                    $errMessage = 'Username/Password Invalid';
                }
            } catch (Exception $ex) {
                $errCode++;
                $errMessage = $ex->getMessage();
            }
        }

        return c::base()->toJsonResponse($errCode, $errMessage, $json);
    }

    if (c::auth()->check()) {
        return c::redirect('admin/home');
    }

    return c::app();
}
```
