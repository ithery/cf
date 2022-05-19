# Form Input - Standard Control
### Text Control

```php
$app = c::app();
$form = $app->addForm();
$textControl = $form->addField()->setLabel('Name')->addTextControl('name');
$textControl->setValue('John Doe');
return $app;
```

### Password Control

```php
$app = c::app();
$form = $app->addForm();
$passwordControl = $form->addField()->setLabel('Password')->addPasswordControl('password');
return $app;
```

### Email Control

```php
$app = c::app();
$form = $app->addForm();
$emailControl = $form->addField()->setLabel('Email')->addEmailControl('name');
$emailControl->setValue('johndoe@example.com');
return $app;
```

### Hidden Control

```php
$app = c::app();
$form = $app->addForm();
$hiddenControl = $form->addHiddenControl('hidden');
$hiddenControl->setValue('John Doe');
return $app;
```
