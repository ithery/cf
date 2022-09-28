# Element - Widget
### Introduction

Element Widget digunakan untuk mempresentasikan ui widget/card

Contoh Sederhana untuk widget


```php

$widget = $app->addWidget()->addClass('mb-3')->setIcon('ti ti-layers')
    ->setTitle(c::__('Basic Information') . ' <span class="text-muted">Dibuat ' . c::formatter()->formatHumanTimeDiff($orgModel->created) . '</span>', false);

$form = $widget->addForm();
$divRow = $form->addDiv()->addClass('row');

$divRow->addDiv()->addClass('col-md-4')->addField()->setLabel('Code')->addControl('code_label', 'label')
    ->setValue('1234');
$divRow->addDiv()->addClass('col-md-4')->addField()->setLabel('Name')->addControl('name_label', 'label')
    ->setValue('John Doe');
$divRow->addDiv()->addClass('col-md-4')->addField()->setLabel('Plan')->addLabelControl()
    ->setValue('Basic');
```
