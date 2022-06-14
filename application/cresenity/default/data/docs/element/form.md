# Element - Form Element
### Introduction

Form element digunakan untuk mempresentasikan ui form html

Contoh Sederhana untuk form change password

```php
    $form = $app->addForm();
    $widget = $form->addWidget();
    $widget->setTitle('Change Password')->setIcon('bs.key');
    $span = $widget->addDiv()->addClass('col-md-6');
    $span->addField()->setLabel('Password')->addPasswordControl('current_password')->setValue($currentPassword);
    $span->addField()->setLabel('New Password')->addPasswordControl('password')->setValue($password);
    $span->addField()->setLabel('Confirmation')->addPasswordControl('confirmation')->setValue($confirmation);
    $actions = $widget->addActionList();
    $actions->setStyle('form-action');
    $actions->addAction()->setLabel('Submit')->setSubmit(true);
```
