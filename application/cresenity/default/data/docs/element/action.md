# Element - Action

The `CElement_Component_Action` component renders a clickable action button. Actions can trigger links, form submissions, AJAX calls, confirmations, or custom JavaScript.

Add an action to CApp using `addAction()`:

```php
$app = c::app();
$action = $app->addAction();
$action->setLabel('Click Me');
$action->addClass('btn btn-primary');

return $app;
```

---

### Link Action

Navigate to a URL when clicked:

```php
$action = $app->addAction();
$action->setLabel('View User');
$action->setIcon('ti ti-eye');
$action->setLink(c::url('admin/user/view/' . $userId));
```

Open in a new tab:

```php
$action->setLinkTarget('_blank');
```

---

### Submit Action

Submit a parent form:

```php
$action = $app->addAction();
$action->setLabel('Save');
$action->setSubmit(true);
$action->addClass('btn btn-success');
```

Submit to a specific URL:

```php
$action->setSubmitTo(c::url('user/save'));
```

---

### Confirm Action

Show a confirmation dialog before executing:

```php
$action = $app->addAction();
$action->setLabel('Delete');
$action->setIcon('ti ti-trash');
$action->setConfirm(true);
$action->setConfirmMessage('Are you sure you want to delete this item?');
$action->setLink(c::url('admin/user/delete/' . $userId));
$action->addClass('btn btn-danger');
```

---

### Disabled Action

```php
$action->setDisabled(true);
```

---

### Active State

```php
$action->setActive(true);
```

---

### Action Options

| Method | Description |
|--------|------------|
| `setLabel($label)` | Button text |
| `setIcon($icon)` | Icon class (e.g. `ti ti-pencil`) |
| `setLink($url)` | Navigate to URL on click |
| `setLinkTarget($target)` | Link target (`_blank`, etc.) |
| `setSubmit($bool)` | Submit parent form |
| `setSubmitTo($url, $target)` | Submit to specific URL |
| `setSubmitValue($value)` | Value sent with submission |
| `setConfirm($bool)` | Show confirmation dialog |
| `setConfirmMessage($msg)` | Custom confirmation message |
| `setDisabled($bool)` | Disable the action |
| `setActive($bool)` | Set active state |
| `setName($name)` | Name attribute |

---

### Event Listeners

Attach event listeners to actions for AJAX operations:

```php
$action = $app->addAction()->setLabel('Reload Data')->addClass('btn btn-primary');
$action->onClickListener()->addReloadHandler()
    ->setUrl(c::url('admin/data/refresh'))
    ->setTarget($targetDiv);
```

```php
$action = $app->addAction()->setLabel('Open Dialog')->addClass('btn btn-info');
$action->onClickListener()->addDialogHandler()
    ->setUrl(c::url('admin/user/edit/' . $userId));
```

---

### Action List

Group multiple actions together using an action list:

```php
$actions = $app->addActionList();
$actions->addAction()->setLabel('Save')->setSubmit(true)->addClass('btn btn-primary');
$actions->addAction()->setLabel('Cancel')->setLink(c::url('admin/user'));
```

With form-action style:

```php
$actions = $form->addActionList();
$actions->setStyle('form-action');
$actions->addAction()->setLabel('Submit')->setSubmit(true);
```
