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

### Prepending Data
#### Prepending Data With Model
```php
    $controlOutlet = $app->addField()->setLabel('Outlet')->addSelectSearchControl('outletId')
    ->setDataFromModel(SEModel_Outlet::class, function ($query) {
        $query->where('outlet_type', 'fisik');
    })->setKeyField('outlet_id')
    ->setFormat(function ($outlet) {
        if (is_array($outlet)) {
            if (carr::get($outlet, 'outlet_id') == 'ALL') {
                return 'ALL';
            }
        }
        $html = '<div>';
        if (SE::isAdmin()) {
            $html .= $outlet->vendor->name . ' - ';
        }
        $html .= $outlet->name . ' <span class="badge badge-success">[' . ucwords($outlet->outlet_type) . ']</span>';
        $html .= '</div>';

        return $html;
    })->setPlaceholder(c::__('Pilih Outlet'))->setValue('')
    ->prependData([
        'outlet_id' => 'ALL',
    ])->setValue('ALL');
```

#### Prepending Data With Query
```php
$controlOutlet = $app->addField()->setLabel('Outlet')->addSelectSearchControl('outletId')
    ->setQuery("select * from outlet where status>0 and outlet_type='fisik'")->setKeyField('outlet_id')
    ->setFormat(function ($outlet) {
        if (is_array($outlet)) {
            if (cstr::startsWith(carr::get($outlet, 'outlet_id'), 'ALL')) {
                return carr::get($outlet, 'outlet_id');
            }
        }
        $html = '<div>';

        $html .= carr::get($outlet, 'name') . ' <span class="badge badge-success">[' . ucwords(carr::get($outlet, 'outlet_type')) . ']</span>';
        $html .= '</div>';

        return $html;
    })->setPlaceholder(c::__('Pilih Outlet'))->setValue('')
    ->prependData([
        'outlet_id' => 'ALL 1',
    ])->prependData([
        'outlet_id' => 'ALL 2',
    ])->prependData([
        'outlet_id' => 'ALL 3',
    ])->setValue('ALL 3')->setPerPage(5);
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
