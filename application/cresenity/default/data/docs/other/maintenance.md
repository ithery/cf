# Maintenance

### Maintenance Mode

Anda dapat mengeset maintenance mode dengan menambahkan file `down.php` pada folder data

contoh file down.php
```php
<?php

return [
    'down' => false,
    'view' => 'system.maintenance',
    'cookie' => 'bypass-maintenance',
];

```

### Down Setting
jika bernilai `true` akan menyatakan bahwa system down dan logic tidak akan dijalankan
(untuk logic bootstrap.php akan tetap dijalankan)

### Cookie Setting
isi cookie ini dengan suatu value untuk membypass system maintenance
jangan set key cookie jika tidak ingin ada cookie untuk bypass

### View Setting
default value `system.maintenance`, overwrite key ini atau override `views/system/maintenance.blade.php` untuk mengganti tampilan maintenance