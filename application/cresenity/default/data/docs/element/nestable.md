# Element - Nestable

The `CElement_Component_Nestable` component renders a drag-and-drop sortable nested list. It is commonly used for managing hierarchical data such as menus, categories, or organizational structures.

Add a nestable using `addElement('nestable')`:

```php
$app = c::app();
$nestable = CElement_Component_Nestable::factory();
$nestable->setDataFromArray([
    ['id' => 1, 'name' => 'Item 1', 'children' => [
        ['id' => 2, 'name' => 'Item 1.1'],
        ['id' => 3, 'name' => 'Item 1.2'],
    ]],
    ['id' => 4, 'name' => 'Item 2'],
]);
$app->add($nestable);

return $app;
```

---

### Data Sources

#### From Array

```php
$nestable->setDataFromArray([
    ['id' => 1, 'name' => 'Parent'],
    ['id' => 2, 'name' => 'Child', 'parent_id' => 1],
]);
```

#### From Model

```php
$nestable->setDataFromModel(CategoryModel::class, function ($q) {
    $q->orderBy('sort_order');
});
```

#### From TreeDB

```php
$nestable->setDataFromTreeDb($treeDb, $parentId);
```

---

### Display Callback

Customize how each item is rendered:

```php
$nestable->setDisplayCallback(function ($row) {
    return '<span>' . carr::get($row, 'name') . '</span>'
        . ' <small class="text-muted">' . carr::get($row, 'code') . '</small>';
});
```

---

### Options

| Method | Description |
|--------|------------|
| `setIdKey($key)` | Set the primary key field (default: `id`) |
| `setValueKey($key)` | Set the value/label key |
| `setCollapsed($bool)` | Start with all items collapsed |
| `disableDnd()` | Disable drag-and-drop |
| `enableDnd()` | Enable drag-and-drop |
| `setHaveCheckbox($bool)` | Show checkboxes on each item |
| `setInput($input)` | Set the hidden input element for storing the order |

---

### Disable Drag and Drop

Use the nestable as a read-only tree display:

```php
$nestable->disableDnd();
```

---

### Checkboxes

Enable checkboxes for selecting items:

```php
$nestable->setHaveCheckbox(true);
```
