# Form Input - Registering Control

You can create custom form input controls by extending `CElement_FormInput` and registering them via the `addControl()` method or by adding a dedicated method to `ControlTrait`.

---

### Using addControl

The generic `addControl()` method creates any registered control by name:

```php
$form->addField()->setLabel('Name')->addControl('name', 'text')->setValue('John');
$form->addField()->setLabel('Status')->addControl('status', 'label')->setValue('Active');
```

---

### Creating a Custom Control

Create a new class in your application's `libraries/` directory:

```php
<?php
class MyApp_FormInput_PhoneNumber extends CElement_FormInput {
    public function __construct($id = null) {
        parent::__construct($id);
        $this->type = 'tel';
        $this->addClass('form-control');
    }

    public function build() {
        $this->tag = 'input';
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
        $this->setAttr('name', $this->name);
        if ($this->placeholder) {
            $this->setAttr('placeholder', $this->placeholder);
        }
    }
}
```

Use it directly:

```php
$control = new MyApp_FormInput_PhoneNumber('phone');
$form->addField()->setLabel('Phone')->add($control);
```

---

### Extending Existing Controls

Extend built-in controls to add default configuration:

```php
<?php
class MyApp_FormInput_CurrencyInput extends CElement_FormInput_AutoNumeric {
    public function __construct($id = null) {
        parent::__construct($id);
        // Set default currency formatting
    }
}
```
