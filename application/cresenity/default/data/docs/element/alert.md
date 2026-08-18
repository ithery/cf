# Element - Alert

The `CElement_Component_Alert` component renders a Bootstrap-style alert box for displaying messages, warnings, and notifications.

Add an alert to CApp using `addAlert()`:

```php
$app = c::app();
$alert = $app->addAlert();
$alert->setType('success');
$alert->add('Record saved successfully.');

return $app;
```

---

### Alert Types

```php
$app->addAlert()->setTypeSuccess()->add('Operation completed.');
$app->addAlert()->setTypeDanger()->add('Something went wrong.');
$app->addAlert()->setTypeWarning()->add('Please check your input.');
$app->addAlert()->setTypeInfo()->add('New update available.');
$app->addAlert()->setTypeError()->add('Failed to save.');
```

| Method | CSS Class |
|--------|-----------|
| `setTypeSuccess()` | `alert-success` |
| `setTypeDanger()` | `alert-danger` |
| `setTypeError()` | `alert-danger` |
| `setTypeWarning()` | `alert-warning` |
| `setTypeInfo()` | `alert-info` |
| `setType($type)` | `alert-{$type}` |

---

### Dismissable Alert

Add a close button so the user can dismiss the alert:

```php
$alert = $app->addAlert();
$alert->setTypeWarning();
$alert->setDismissable(true);
$alert->add('This alert can be dismissed.');
```

---

### Alert with Title

```php
$alert = $app->addAlert();
$alert->setTypeInfo();
$alert->setTitle('Notice');
$alert->add('Your session will expire in 5 minutes.');
```
