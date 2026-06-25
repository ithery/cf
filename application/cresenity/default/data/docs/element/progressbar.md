# Element - Progress Bar

The `CElement_Component_ProgressBar` component renders an animated progress bar with support for real-time progress updates via a background process.

Create a progress bar using the factory:

```php
$app = c::app();
$progressBar = CElement_Component_ProgressBar::factory();
$progressBar->setValue(65);
$app->add($progressBar);

return $app;
```

---

### Setting Value

Set the progress value (0-100 by default):

```php
$progressBar = CElement_Component_ProgressBar::factory();
$progressBar->setValue(75);
$app->add($progressBar);
```

---

### With Background Process

Attach a background process that updates the progress bar in real-time:

```php
$progressBar = CElement_Component_ProgressBar::factory();
$progressBar->withProcess($processCallback);
$app->add($progressBar);
```

The process runs in a hidden iframe and updates the progress bar via JavaScript as work is completed.

---

### Styling

Add CSS classes to customize appearance:

```php
$progressBar = CElement_Component_ProgressBar::factory();
$progressBar->setValue(50);
$progressBar->addClass('mb-3');
$app->add($progressBar);
```
