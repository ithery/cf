# Element - Tree View

The `CElement_Component_TreeView` component renders an interactive tree structure using jsTree. It is used for displaying hierarchical data such as file structures, categories, or organizational charts.

Create a tree view:

```php
$app = c::app();
$tree = CElement_Component_TreeView::factory();
$tree->setData([
    ['text' => 'Root Node', 'children' => [
        ['text' => 'Child 1'],
        ['text' => 'Child 2', 'children' => [
            ['text' => 'Grandchild 1'],
            ['text' => 'Grandchild 2'],
        ]],
    ]],
    ['text' => 'Another Root'],
]);
$app->add($tree);

return $app;
```

---

### Data Format

The tree data follows the jsTree format. Each node is an array with:

| Key | Description |
|-----|------------|
| `text` | Display text for the node |
| `children` | Array of child nodes (optional) |
| `icon` | Custom icon class (optional) |

```php
$tree->setData([
    [
        'text' => 'Documents',
        'icon' => 'fas fa-folder',
        'children' => [
            ['text' => 'report.pdf', 'icon' => 'fas fa-file-pdf'],
            ['text' => 'notes.txt', 'icon' => 'fas fa-file-alt'],
        ],
    ],
    [
        'text' => 'Images',
        'icon' => 'fas fa-folder',
        'children' => [
            ['text' => 'photo.jpg', 'icon' => 'fas fa-file-image'],
        ],
    ],
]);
```

---

### Use Case

Tree views are ideal for file browsers, category hierarchies, menu builders, and any data with parent-child relationships. For drag-and-drop reordering support, consider using the [Nestable](/docs/element/nestable) component instead.
