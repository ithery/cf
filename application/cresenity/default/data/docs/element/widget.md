# Element - Widget

The `CElement_Component_Widget` component renders a card/panel container with an optional header (title and icon), content area, and header actions.

Add a widget to CApp using `addWidget()`:

```php
$app = c::app();
$widget = $app->addWidget();
$widget->setTitle('User Information');
$widget->addDiv()->add('Widget content here');

return $app;
```

---

### Title and Icon

```php
$widget = $app->addWidget();
$widget->setTitle('Basic Information');
$widget->setIcon('ti ti-layers');
```

Title with raw HTML (pass `false` as second argument to disable translation):

```php
$widget->setTitle(
    c::__('Basic Information') . ' <span class="text-muted">Updated 2h ago</span>',
    false
);
```

---

### Content

Add any elements inside the widget:

```php
$widget = $app->addWidget()->setTitle('User Details');

$row = $widget->addDiv()->addClass('row');
$row->addDiv()->addClass('col-md-4')
    ->addField()->setLabel('Code')->addLabelControl()->setValue('1234');
$row->addDiv()->addClass('col-md-4')
    ->addField()->setLabel('Name')->addLabelControl()->setValue('John Doe');
$row->addDiv()->addClass('col-md-4')
    ->addField()->setLabel('Plan')->addLabelControl()->setValue('Basic');
```

---

### Header Actions

Add action buttons to the widget header:

```php
$widget = $app->addWidget()->setTitle('Users');

$widget->addHeaderAction()
    ->setLabel('Add User')
    ->setIcon('ti ti-plus')
    ->setLink(c::url('admin/user/add'));

$widget->addHeaderAction()
    ->setLabel('Export')
    ->setIcon('ti ti-download');
```

Set header action style:

```php
$widget->setHeaderActionStyle('btn-group');
```

---

### No Padding

Remove the default padding from the widget content area:

```php
$widget->setNoPadding(true);
```

---

### Switcher

Add a toggle switcher to the widget header:

```php
$switcher = $widget->addSwitcher();
$widget->setSwitcherBehaviour('hide');
```

---

### Widget with Form

A common pattern for edit pages:

```php
$form = $app->addForm();
$widget = $form->addWidget();
$widget->setTitle('Edit Profile')->setIcon('ti ti-user');

$widget->addField()->setLabel('Name')->addTextControl('name')->setValue($user->name);
$widget->addField()->setLabel('Email')->addEmailControl('email')->setValue($user->email);

$actions = $widget->addActionList();
$actions->setStyle('form-action');
$actions->addAction()->setLabel('Save')->setSubmit(true);
$actions->addAction()->setLabel('Cancel')->setLink(c::url('admin/user'));
```

---

### Widget with Table

```php
$widget = $app->addWidget()->setTitle('Recent Orders');
$widget->setNoPadding(true);

$table = $widget->addTable();
$table->setDataFromModel(OrderModel::class);
$table->addColumn('order_no')->setLabel('Order #');
$table->addColumn('total')->setLabel('Total');
$table->addColumn('status')->setLabel('Status');
```
