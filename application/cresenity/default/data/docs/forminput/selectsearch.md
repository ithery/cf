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


### Extending SelectSearch

```php
<?php

class APPFormInput_SelectSearch_UserOrgSelect extends CElement_FormInput_SelectSearch {
    protected function build() {
        parent::build();
        $this->setPlaceholder(c::__('Search user'));
        $this->setDataFromModel(XPModel_User::class, function (CModel_Query $query) {
            $query->whereHas('orgJoined', function ($q) {
                $q->where('user_org.org_id', '=', XP::orgId());
            });
        });
        $this->setKeyField('user_id');
        $this->setSearchField(['username']);
    }
}
```
