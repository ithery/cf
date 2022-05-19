# Cres JS - UI

### Waves Effect (>=1.4)


Tambahkan config pada `cresjs.php`

```php
<?php

return [
    //...
    'waves' => [
        'selector' => '.btn'
    ]
    //....
];
```

Pada contoh diatas maka seluruh selector dengan class `.btn` akan mempunyai waves effect

Jika tidak dikonfigurasi, secara default cresjs akan mengeksekusi element yang mempunyai `.cres-waves-effect`
