# Form Input - Color Picker

Mini color picker control for selecting colors.

---

### Basic Usage

```php
$form->addField()->setLabel('Theme Color')->addMiniColorControl('color')
    ->setValue('#cc131f');
```

The control renders as a text input with a swatch attached. The submitted value is the colour
in hexadecimal form, so it can be stored in a plain `varchar` column and used directly in CSS.

### Placeholder

```php
$form->addField()->setLabel('Theme Color')->addMiniColorControl('color')
    ->setPlaceholder('#000000');
```

### Reading the value

The value arrives in `$_POST` as the hexadecimal string shown in the input:

```php
$post = $_POST;
$color = carr::get($post, 'color');

// #cc131f
```

An empty input submits an empty string rather than a default colour, so a fallback belongs in
the controller:

```php
$color = carr::get($post, 'color') ?: '#000000';
```

### Underlying library

The control is backed by the `minicolors` asset module, which is registered automatically when
the control is created. The picker opens below and to the left of the input and uses the hue
control.
