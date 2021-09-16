# View

### Introduction

Views menyediakan cara yang mudah untuk menempatkan semua HTML kita dalam file terpisah. Views memisahkan controller logic dari presentation logic.

Views disimpan di direktori `views`. Tampilan sederhana mungkin terlihat seperti ini:

```php
<html>
    <body>
        <h1>Hello, {{ $name }}</h1>
    </body>
</html>
```

### Creating & Rendering Views

Anda dapat membuat tampilan dengan menempatkan file dengan ekstensi .blade.php di direktori `views` aplikasi Anda. Ekstensi .blade.php menginformasikan kerangka kerja bahwa file tersebut berisi template Blade. Template Blade berisi HTML Blade directives yang memungkinkan Anda untuk dengan mudah echo values, create "if" statements, iterate over data, dan banyak lagi.

Setelah membuat view, anda dapat melakukan return dari controller aplikasi dengan menggunakan c helper:

```php
return c::view('greeting', ['name' => 'James']);
```

Views dapat juga di return menggunakan class CView:

```php
return CView::make('greeting', ['name' => 'James']);
```

### Nested View Directories

Views dapat jg ditempatkan dalam subdirectory. notasi "Dot" dapat digunakan untuk mereferensikan views yang ada dalam subdirectory. Sebagai contoh, jika view anda disimpan pada `views/admin/profie.blade.php` , anda dapat menuliskan pada controller aplikasi seperti:

```php
return c::view('admin.profile', $data);
```

### Determining If A View Exists

Jika anda memerlukan untuk mengechek apakah view tersedia, anda dapat menggunakan object CView. `exists` method akan mengembalikan nilai `true` jika view tersedia.

```php
if (CView::exists('admin.profile')) {
    //
}
```

### Passing Data To Views

Seperti yang anda lihat pada contoh-contoh sebelumnya, anda dapat melakukan passing data dari controller ke view sehingga data tersebut dapat digunakan dalam view.

```php
return c::view('greetings', ['name' => 'Victoria']);
```

Saat melakukan passingg data dengan cara diatas, data harus berupa array key / value. Setelah memprovide data ke view, anda dapat mengakses tiap value didalam view anda dengan key sebagai nama variable, Contoh `<?php echo $name; ?>.`

sebagai alternatif, anda dapat melakukan passing data ke view secara satu persatu dengan `with` method.
`with` method me-return kembali instance view object, jadi anda dapat melakukan chaining methods:

```php
return c::view('greeting')
    ->with('name', 'Victoria')
    ->with('occupation', 'Astronaut');
```
