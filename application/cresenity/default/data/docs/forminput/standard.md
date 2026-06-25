# Form Input - Standard Control

Standard controls map directly to HTML5 input elements. They are the basic building blocks for forms.

---

### Text

```php
$form->addField()->setLabel('Name')->addTextControl('name')
    ->setValue('John Doe')
    ->setPlaceholder('Enter name...');
```

### Password

```php
$form->addField()->setLabel('Password')->addPasswordControl('password')
    ->setPlaceholder('Enter password...')
    ->setToggleVisibility(true);
```

### Email

```php
$form->addField()->setLabel('Email')->addEmailControl('email')
    ->setValue('user@example.com');
```

### Number

```php
$form->addField()->setLabel('Quantity')->addNumberControl('qty')
    ->setValue(1);
```

### Textarea

```php
$form->addField()->setLabel('Description')->addTextareaControl('description')
    ->setValue('Some long text...');
```

### Hidden

```php
$form->addHiddenControl('user_id')->setValue(123);
```

### CSRF

Adds a hidden CSRF token field for form security:

```php
$form->addCsrfControl();
```

### Select

Static dropdown select:

```php
$form->addField()->setLabel('Role')->addSelectControl('role')
    ->setList([
        'admin' => 'Administrator',
        'user' => 'User',
        'guest' => 'Guest',
    ])
    ->setValue('user');
```

### Checkbox

```php
$form->addField()->setLabel('Active')->addCheckboxControl('is_active')
    ->setValue(1);
```

### Checkbox List

Multiple checkboxes from a list:

```php
$form->addField()->setLabel('Permissions')->addCheckboxListControl('permissions')
    ->setList([
        'read' => 'Read',
        'write' => 'Write',
        'delete' => 'Delete',
    ]);
```

### Radio

```php
$form->addField()->setLabel('Gender')->addRadioControl('gender')
    ->setValue('male');
```

### Radio List

```php
$form->addField()->setLabel('Plan')->addRadioListControl('plan')
    ->setList([
        'basic' => 'Basic',
        'pro' => 'Professional',
        'enterprise' => 'Enterprise',
    ]);
```

### File

```php
$form->addField()->setLabel('Document')->addFileControl('document');
```

### Image

```php
$form->addField()->setLabel('Photo')->addImageControl('photo');
```

### Date

```php
$form->addField()->setLabel('Birthday')->addDateControl('birthday')
    ->setValue('2000-01-01');
```

### Time

```php
$form->addField()->setLabel('Start Time')->addTimeControl('start_time');
```

### Range

```php
$form->addField()->setLabel('Volume')->addRangeControl('volume');
```

### Label

Display-only field (non-editable):

```php
$form->addField()->setLabel('Status')->addLabelControl()
    ->setValue('Active');
```

---

### Common Methods

All controls share these methods:

| Method | Description |
|--------|------------|
| `setValue($value)` | Set the control value |
| `setName($name)` | Set the input name attribute |
| `setDisabled($bool)` | Disable the control |
| `setReadonly($bool)` | Make the control read-only |
| `setPlaceholder($text)` | Set placeholder text (where supported) |
| `addValidation($name, $value)` | Add client-side validation rule |
