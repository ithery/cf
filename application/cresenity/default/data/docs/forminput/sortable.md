# Form Input - Sortable

Drag-and-drop sortable list control for reordering items.

---

### Basic Usage

```php
$form->addField()->setLabel('Sort Order')->addSortableControl('order');
```

### Providing the items

The items to reorder are supplied with `setList()`, keyed by the value that should be
submitted:

```php
$form->addField()->setLabel('Menu Order')->addSortableControl('menu_order')
    ->setList([
        'home' => 'Home',
        'product' => 'Product',
        'about' => 'About Us',
    ]);
```

The control renders one draggable row per entry, showing the label and submitting the key.

### Setting the current order

`setValue()` takes an array of keys in the order they should appear. Keys listed here are
rendered first, in the given sequence:

```php
$form->addField()->setLabel('Menu Order')->addSortableControl('menu_order')
    ->setList([
        'home' => 'Home',
        'product' => 'Product',
        'about' => 'About Us',
    ])
    ->setValue(['product', 'home', 'about']);
```

When no value is set, the list is rendered in the order it was supplied.

### Submitted value

The resulting order is written into a hidden input, so the field arrives in `$_POST` as an
ordered array of keys:

```php
$post = $_POST;
$order = carr::get($post, 'menu_order');

// ['product', 'home', 'about']
```

Persisting the order is a matter of storing each key's position:

```php
foreach (carr::get($post, 'menu_order', []) as $index => $code) {
    $model = MenuModel::where('code', $code)->first();
    $model->sort_order = $index + 1;
    $model->save();
}
```
