# Element - Form

The `CElement_Component_Form` component provides a form builder for creating HTML forms with fields, validation, and AJAX submission.

Add a form to CApp using `addForm()`:

```php
$app = c::app();
$form = $app->addForm();
$form->addField()->setLabel('Name')->addTextControl('name');
$form->addField()->setLabel('Email')->addEmailControl('email');

return $app;
```

---

### Form Configuration

```php
$form = $app->addForm();
$form->setAction(c::url('user/save'));
$form->setMethod('post');
$form->setName('user-form');
$form->setEncTypeMultiPartFormData();
$form->setAutoComplete(false);
```

| Method | Description |
|--------|------------|
| `setAction($url)` | Form action URL |
| `setMethod($method)` | HTTP method (`get` or `post`) |
| `setName($name)` | Form name attribute |
| `setTarget($target)` | Form target attribute |
| `setEncType($type)` | Form encoding type |
| `setEncTypeMultiPartFormData()` | Shorthand for multipart form data (file uploads) |
| `setAutoComplete($bool)` | Enable/disable browser autocomplete |
| `setLayout($layout)` | Form layout (`horizontal`, etc.) |

---

### Fields

Add form fields using `addField()`. Each field has a label and a control (input):

```php
$form->addField()->setLabel('Username')->addTextControl('username');
$form->addField()->setLabel('Email')->addEmailControl('email');
$form->addField()->setLabel('Password')->addPasswordControl('password');
$form->addField()->setLabel('Bio')->addTextareaControl('bio');
```

See [Form Input - Standard](/docs/forminput/standard) for all available input controls.

---

### Layout with Columns

Use div containers to create multi-column layouts:

```php
$form = $app->addForm();
$row = $form->addDiv()->addClass('row');

$col1 = $row->addDiv()->addClass('col-md-6');
$col1->addField()->setLabel('First Name')->addTextControl('first_name');
$col1->addField()->setLabel('Email')->addEmailControl('email');

$col2 = $row->addDiv()->addClass('col-md-6');
$col2->addField()->setLabel('Last Name')->addTextControl('last_name');
$col2->addField()->setLabel('Phone')->addTextControl('phone');
```

---

### Form Inside Widget

A common pattern is wrapping a form inside a widget for a card-like appearance:

```php
$form = $app->addForm();
$widget = $form->addWidget();
$widget->setTitle('Change Password')->setIcon('bs.key');

$col = $widget->addDiv()->addClass('col-md-6');
$col->addField()->setLabel('Current Password')->addPasswordControl('current_password');
$col->addField()->setLabel('New Password')->addPasswordControl('password');
$col->addField()->setLabel('Confirmation')->addPasswordControl('confirmation');

$actions = $widget->addActionList();
$actions->setStyle('form-action');
$actions->addAction()->setLabel('Submit')->setSubmit(true);
```

---

### AJAX Submission

Submit the form via AJAX instead of a full page reload:

```php
$form->setAjaxSubmit(true);
```

Or use a submit listener for more control:

```php
$form->onSubmitListener()
    ->addAjaxSubmitHandler()
    ->setUrl(c::url('user/save'))
    ->setHandleJsonResponse(true);
```

---

### Validation

Enable client-side validation:

```php
$form->setValidation(true);
$form->setValidationPromptPosition('topRight');
```

---

### Action Buttons

Add action buttons (submit, reset, etc.) using an action list:

```php
$actions = $form->addActionList();
$actions->setStyle('form-action');
$actions->addAction()->setLabel('Save')->setSubmit(true);
$actions->addAction()->setLabel('Cancel')
    ->setLink(c::url('admin/user'));
```
