# Form Input - Select Search

### Introduction

Contoh Kode:

```php
$form->addField()->setLabel('User')->addSelectSearchControl('userId')
    ->setDataFromModel(CApp_Model_Users::class, function ($q) {
        $q->where('column','=','something');
    })
    ->setKeyField('user_id')
    ->setSearchField('username')
    ->setFormatResult('{username}')
    ->setValue($deviceId);
```
