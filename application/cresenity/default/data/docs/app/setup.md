# Application - Setup
### Setup View

Secara default CApp akan meload default views `page.blade.php`. anda dapat mengoverride view tersebut pada folder views yang ada project anda.


```html
<!DOCTYPE html>
<html class="no-js material-style" lang="{{ c::locale() }}" >
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @CAppSeo
    <link rel="icon" href="{{ c::media('img/favico.png') }}">
    @CAppStyles
</head>
<body>
    @CAppContent

    @CAppScripts

    <script>
        window.addEventListener('cresenity:loaded',()=>{
            //do something after object cresenity loaded
        });
    </script>

</body>
</html>

```

- `@CAppSeo` adalah konfigurasi seo `c::app()->seo()`
- `@CAppStyles` adalah css yang diperlukan oleh CApp, ini dapat diset dari theme atau secara runtime menggunakan `c::manager()->registerCss`
- `@CAppContent` adalah isi content yang digenerate oleh CApp
- `@CAppScripts` adalah js yang diperlukan oleh CApp, ini dapat diset dari theme atau secara runtime menggunakan `c::manager()->registerJs`
