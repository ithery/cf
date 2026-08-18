# Element - List Group

The `CElement_Component_ListGroup` component renders a list of items, similar to Bootstrap's list group. Each item can be customized with a callback.

Add a list group using `addListGroup()`:

```php
$app = c::app();
$listGroup = $app->addListGroup();
$listGroup->addItem()->add('First item');
$listGroup->addItem()->add('Second item');
$listGroup->addItem()->add('Third item');

return $app;
```

---

### Items

Add items using `addItem()`. Each item is a `CElement_Component_ListGroup_Item` that can contain any element:

```php
$listGroup = $app->addListGroup();

$item = $listGroup->addItem();
$item->add('<strong>John Doe</strong>');
$item->add('<p class="mb-0">Software Engineer</p>');
```

---

### Item Callback

Use `setItemCallback` to customize how each item is rendered when data is loaded dynamically:

```php
$listGroup = $app->addListGroup();
$listGroup->setItemCallback(function ($item, $data) {
    $item->add('<h5>' . carr::get($data, 'name') . '</h5>');
    $item->add('<p>' . carr::get($data, 'description') . '</p>');
});
```

---

### AJAX Loading

Load list group items via AJAX:

```php
$listGroup = $app->addListGroup();
$listGroup->setAjax(true);
```

---

### Item with Data

Pass data to individual items:

```php
$item = $listGroup->addItem();
$item->setData(['name' => 'John', 'role' => 'Admin']);
$item->setIndex(0);
```
