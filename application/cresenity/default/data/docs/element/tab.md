# Element - Tab
### Introduction

Tab digunakan untuk mempresentasikan ui tab

Contoh Sederhana untuk tab

```php
$tabList = $app->addTabList()->setTabPosition('top');
$tab1 = $tabList->addTab()->setLabel('Tab 1')
    ->addDiv()->add('Tab 1');
$tab2 = $tabList->addTab()->setLabel('Tab 2')
    ->addDiv()->add('Tab 2');
```

### Ajax
```php
$tabList = $app->addTabList()->setTabPosition('top');
$tabLog = $tabList->addTab()->setLabel('Activity')
    ->setAjaxUrl(curl::base() . "panel/data/device/manage/log/index/${deviceId}")
    ->setActive($tab == 'log');
$tabLog->setNoPadding()->setIcon('ti ti-timer');
$tabDebug = $tabList->addTab()->setLabel('Debug')
    ->setAjaxUrl(curl::base() . "panel/data/device/manage/debug/index/${deviceId}")
    ->setActive($tab == 'debug');
$tabDebug->setNoPadding()->setIcon('ti ti-help-alt');
```
