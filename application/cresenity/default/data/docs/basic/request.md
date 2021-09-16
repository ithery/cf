# Request

### Introduction

`CHTTP::request()` akan mengembalikan object CHTTP_Request dengan request sekarang dilakukan. dari object ini anda dapat melakukan retrieve input, cookies, dan files yang disubmit oleh HTTP request.

### Accessing The Request

untuk mendapat current HTTP request, anda dapat memanggil `CHTTP::request()`:

```php
<?php


class Controller_User extends CController {
   /**
     * Store a new user.
     *
     * @return CHTTP_Response
     */
    public function store(){
        $request = CHTTP::request();
        $name = $request->input('name');

        //
    }
}

```


### Request Path & Method

`CHTTP_Request` adalah turunan dari `Symfony\Component\HttpFoundation\Request` class. Kita akan membahas beberapa fungsi yang cukup penting pada class ini:

##### Retrieving The Request Path

fungsi `path` akan mengembalikan nilai request path. Jika incoming request berasal dari `http://example.com/foo/bar`, fungsi path akan mendapatkan nilai `foo/bar`:

```php
$uri = $request->path();
```


##### Inspecting The Request Path / Route
fungsi `is` dapat memungkinkan kita untuk mengecheck apakah incoming request path cocok dengan suatu pattern. Kita dapat menggunakan karakter `*` sebagai wildcard saat menggunkan fungsi ini:

```php
if ($request->is('admin/*')) {
    //
}
```

##### Retrieving The Request URL

Untuk mendapatkan full URL dari incoming request, kita dapat menggunakan fungsi `url` atau `fullUrl`. fungsi `url` akan mengembalikan url tanpa query string, sedangkan fungsi `fullUrl` akan menyertakan query string:

```php
$url = $request->url();

$urlWithQueryString = $request->fullUrl();
```

Jika kita ingin menambahkan query string data pada current URL, kita dapat melakukan pemanggilan fungsi `fullUrlWithQuery`.fungsi ini akan menggabungkan array yang diberikan dengan query string yang ada pada request:

```php
$request->fullUrlWithQuery(['type' => 'phone']);
```

##### Retrieving The Request Method

fungsi `method` akan mengembalikan nilai HTTP verb pada request. Kita dapat menggunakan fungsi `isMethod` untuk mengecheck jika HTTP verb cocok dengan suatu string:

```php
$method = $request->method();

if ($request->isMethod('post')) {
    //
}
```
