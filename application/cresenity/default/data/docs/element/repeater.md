# Element - Repeater

The `CElement_Component_Repeater` component renders a dynamic form section where users can add and remove rows of input fields. It is commonly used for line items, multi-entry forms, and dynamic lists.

Create a repeater:

```php
$app = c::app();
$form = $app->addForm();

$repeater = CElement_Component_Repeater::factory();
$repeater->setItemBuilder(function ($item) {
    $row = $item->addDiv()->addClass('row');
    $row->addDiv()->addClass('col-md-6')
        ->addField()->setLabel('Name')->addTextControl('name[]');
    $row->addDiv()->addClass('col-md-6')
        ->addField()->setLabel('Email')->addEmailControl('email[]');
});
$form->add($repeater);

return $app;
```

---

### Item Builder

Define the content of each repeatable row using `setItemBuilder()`. The callback receives a container element:

```php
$repeater->setItemBuilder(function ($item) {
    $item->addField()->setLabel('Product')->addTextControl('product[]');
    $item->addField()->setLabel('Quantity')->addTextControl('qty[]');
    $item->addField()->setLabel('Price')->addAutoNumericControl('price[]');
});
```

Each row automatically gets an "Delete" button to remove it, and an "New Item" button is shown at the bottom to add more rows.

---

### Minimum Items

Set the minimum number of rows that must always be present:

```php
$repeater->setMinItem(1);  // default: 1
$repeater->setMinItem(3);  // start with 3 rows, cannot go below 3
```

---

### Use Case

Repeaters are ideal for:

- Invoice line items (product, quantity, price)
- Multiple address entries
- Dynamic key-value pairs
- Any form where the number of entries is variable
