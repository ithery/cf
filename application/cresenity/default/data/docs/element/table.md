# Element - Table Element
### Introduction

Element Table digunakan untuk mempresentasikan ui table


### Table Data

##### setDataFromArray
setDataFromArray melakukan pengisian data ke datatable secara langsung dari array
method setDataFromArray tidak direkomendasikan jika data array sangat banyak

```php
$app = c::app();
$table = $app->addTable();
$table->setDataFromArray([
    [
        'role'=>'Developer',
        'username'=>'albert',
        'name'=>'Albert',
    ],
    [
        'role'=>'QA',
        'username'=>'adam',
        'name'=>'Adam',
    ],
]);
$table->addColumn('role')->setLabel('Role');
$table->addColumn('username')->setLabel('Username');
$table->addColumn('name')->setLabel('Nama');
return $app;
```

##### setDataFromQuery
Table dapat diisi dengan data langsung dari perintah sql command.

```php
$app = c::app();
$table $app->addTable();
$q = 'select u.*, r.name as role_name
    from users as u inner join roles as r on r.role_id=u.role_id
    where u.status>0
';
$table->setDataFromQuery($q);
$table->addColumn('role_name')->setLabel('Role');
$table->addColumn('username')->setLabel('Username');
$table->addColumn('name')->setLabel('Nama');
return $app;
```


##### setDataFromModel
```php
$app = c::app();
$table $app->addTable();
$table->setDataFromModel(CModel_App_User::class,function(CModel_Query $q){
    //$q->with(['role']); agar role di load secara eager loading saat iterasi table
    $q->with(['role']);
});
$table->addColumn('role.name')->setLabel('Role');
$table->addColumn('username')->setLabel('Username');
$table->addColumn('name')->setLabel('Role');
return $app;
```

---

Koneksi yang digunakan pada table adalah sesuai koneksi `default` pada config `database.php`


### Column Callback

```php
$table->addColumn('request')->setLabel('Request')->setCallback(function ($row, $value) {
    return CElement_Component_ShowMore::factory()->addClass('whitespace-pre')->add(json_encode(json_decode($value, true), JSON_PRETTY_PRINT));
});
```



### Translations/Label

Secara default translation dapat dicopy dari path `{DOCROOT}system/i18n/en_US/element/datatable.php`
setelah dicopy ke path yang sesuai pada application maka labels otomatis dapat di overwrite dengan yang baru

Key language sama dengan key javascript datatables yang dapat dilihat pada documentation datatable

<a href="https://datatables.net/plug-ins/i18n/English.html" target="_blank">Data Table Language Documentation</a>


<br/><br/>
Contoh isi file translation:


```php
<?php

return  [
    'emptyTable' => 'No data available in table',
    'info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
    'infoEmpty' => 'Showing 0 to 0 of 0 entries',
    'infoFiltered' => '(filtered from _MAX_ total entries)',
    'infoThousands' => ',',
    'lengthMenu' => 'Show _MENU_ entries',
    'loadingRecords' => 'Loading...',
    'processing' => 'Processing...',
    'search' => 'Search',
    'zeroRecords' => 'No matching records found',
    'thousands' => ',',
    'paginate' => [
        'first' => 'First',
        'last' => 'Last',
        'next' => 'Next',
        'previous' => 'Previous'
    ],
    // ...
]
```

### Row Actions

Contoh kode untuk ubah label sesuai condition:
```php
$table->addRowAction()->withRowCallback(function ($element, $row) {
    $isActive = carr::get($row, 'is_active');
    $element->setIcon($isActive ? 'ti ti-close' : 'ti ti-check')
        ->setLabel($isActive ? 'Non Aktifkan' : 'Aktifkan')
        ->setLink(c::url('url/toactivate/or/nonactivate/customer/{customer_id}'));
});
```

Contoh kode untuk hide / show action saat kondisi tertentu
```php
$table->addRowAction()->withRowCallback(function ($element, $row) {
    $isLocked = carr::get($row, 'is_locked');
    if (!$isLocked) {
        $element->setVisibility(true);
        $element->setIcon('ti ti-trash')
            ->setLabel('Delete')
            ->setConfirm(true)
            ->setLink(c::url('url/todelete/{something_id}'));
    } else {
        $element->setVisibility(false);
    }
});
```
