# Application - Other

### Messages

Display flash messages to the user:

```php
<?php
// Set messages
c::app()->message('success', 'Record saved successfully');
c::app()->message('error', 'Something went wrong');
c::app()->message('warning', 'Please check your input');
c::app()->message('info', 'New update available');

// Messages are automatically rendered in the page
```

### Custom Data

Pass custom data to the page layout view:

```php
<?php
$app = c::app();
$app->setData('activeMenu', 'dashboard');
$app->setData('breadcrumbs', [
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'Dashboard'],
]);

return $app;
```

Access custom data in the layout view:

```html
@if(isset($breadcrumbs))
    @foreach($breadcrumbs as $crumb)
        <span>{{ $crumb['label'] }}</span>
    @endforeach
@endif
```

### Custom JavaScript

Add inline JavaScript to the page:

```php
$app->addCustomJs("console.log('Page loaded');");
```

### AJAX Data

Set data that will be included in AJAX responses:

```php
$app->setAjaxData('redirect', '/app/home');
$app->setAjaxData('notification', [
    'type' => 'success',
    'message' => 'Saved',
]);
```

### Scroll to Top

Enable or disable the scroll-to-top button:

```php
CApp::setHaveScrollToTop(true);
```

### Validation

Validate data directly from CApp:

```php
$validated = c::app()->validate($data, [
    'name' => 'required|max:255',
    'email' => 'required|email',
]);
```

### Translator

Access the translator for multi-language support:

```php
$translator = c::app()->translator();

// Or use the c helper
$text = c::__('Welcome');
$text = c::__('Hello, :name', ['name' => 'John']);
```
