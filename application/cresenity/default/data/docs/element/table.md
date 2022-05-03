# Element - Table
### Introduction

Table element digunakan untuk mempresentasikan ui table

### Table Data

##### setDataFromArray
setDataFromArray melakukan pengisian data ke datatable secara langsung dari array
method setDataFromArray tidak direkomendasikan jika data array sangat banyak

```php
$app = c::app();
$table $app->addTable();
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
