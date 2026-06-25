# Form Input - Standard Control

All form input controls are added to a field using `addXxxControl()` methods. Each control returns an object that can be configured with `setValue()`, `setPlaceholder()`, `setDisabled()`, `setReadonly()`, and other methods.

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
    ->setPlaceholder('Enter password...');
```

Show/hide toggle:

```php
$form->addField()->setLabel('Password')->addPasswordControl('password')
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

### Auto Numeric

Formatted number input with currency support:

```php
$form->addField()->setLabel('Price')->addAutoNumericControl('price')
    ->setValue(50000);
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

### Label

Display-only field (non-editable):

```php
$form->addField()->setLabel('Status')->addLabelControl()
    ->setValue('Active');
```

---

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

### Select Search (Select2)

AJAX-powered searchable select. See [Select Search](/docs/forminput/selectsearch) for full documentation.

```php
$form->addField()->setLabel('Country')->addSelectSearchControl('country')
    ->setDataFromModel(CountryModel::class)
    ->setKeyField('code')
    ->setSearchField('name');
```

### Select Two (cres.js)

Modern Select2 with cres.js auto-initialization. Works inside Repeater.

```php
$form->addField()->setLabel('Country')->addSelectTwoControl('country')
    ->setDataFromModel(CountryModel::class)
    ->setKeyField('code')
    ->setSearchField('name');
```

---

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

Multiple radio buttons from a list:

```php
$form->addField()->setLabel('Plan')->addRadioListControl('plan')
    ->setList([
        'basic' => 'Basic',
        'pro' => 'Professional',
        'enterprise' => 'Enterprise',
    ]);
```

---

### Date

```php
$form->addField()->setLabel('Birthday')->addDateControl('birthday')
    ->setValue('2000-01-01');
```

### Time

```php
$form->addField()->setLabel('Start Time')->addTimeControl('start_time');
```

### Date Time

```php
$form->addField()->setLabel('Event Date')->addDateTimeModalControl('event_date');
```

### Date Range

```php
$form->addField()->setLabel('Period')->addDateRangeDropdownButtonControl('period');
```

---

### File

```php
$form->addField()->setLabel('Document')->addFileControl('document');
```

### File (AJAX)

Upload file via AJAX without form submit:

```php
$form->addField()->setLabel('Document')->addFileAjaxControl('document');
```

### Multiple File (AJAX)

```php
$form->addField()->setLabel('Attachments')->addMultipleFileAjaxControl('attachments');
```

### Image

```php
$form->addField()->setLabel('Photo')->addImageControl('photo');
```

### Image (AJAX)

Upload image via AJAX with preview:

```php
$form->addField()->setLabel('Avatar')->addImageAjaxControl('avatar');
```

### Multiple Image (AJAX)

```php
$form->addField()->setLabel('Gallery')->addMultipleImageAjaxControl('gallery');
```

---

### Editor JS

Block-style rich text editor:

```php
$form->addField()->setLabel('Content')->addEditorJsControl('content');
```

### Summernote

WYSIWYG rich text editor:

```php
$form->addField()->setLabel('Body')->addSummerNoteControl('body');
```

### Color Picker

```php
$form->addField()->setLabel('Color')->addMiniColorControl('color')
    ->setValue('#cc131f');
```

### Range

```php
$form->addField()->setLabel('Volume')->addRangeControl('volume');
```

### Sortable

Drag-and-drop sortable list:

```php
$form->addField()->setLabel('Order')->addSortableControl('order');
```

### Query Builder

Visual query builder for creating filter conditions:

```php
$form->addField()->setLabel('Filter')->addQueryBuilderControl('filter');
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
