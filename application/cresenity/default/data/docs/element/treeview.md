# Element - Tree View

The `CElement_Component_TreeView` component renders an interactive jsTree-backed tree — file structures, categories, org charts. Rendering/behavior is handled client-side by cres.js, driven server-side by a `cres-config` attribute built from a `CElement_Component_TreeView_Node` tree.

Add one using `addTreeView()`:

```php
$app = c::app();
$tree = $app->addTreeView();
$tree->setNodes([
    ['text' => 'Documents', 'icon' => 'fas fa-folder', 'children' => [
        ['text' => 'report.pdf', 'icon' => 'fas fa-file-pdf'],
        ['text' => 'notes.txt', 'icon' => 'fas fa-file-alt'],
    ]],
    ['text' => 'Another Root'],
]);

return $app;
```

---

### Fixed Data

`setNodes(array $nodes)` replaces the whole tree. Each entry is either a `CElement_Component_TreeView_Node`, a plain array (`text`, `icon`, `children`, `id`), or a bare string (used as `text`):

| Key | Description |
|-----|------------|
| `text` | Display text for the node |
| `icon` | Custom icon class (optional) |
| `children` | Array of child nodes (optional) |
| `id` | Node id, useful when the node needs to be referenced later (optional) |

You can also build the tree imperatively via `getNodes()`, which returns the root `CElement_Component_TreeView_Node`:

```php
$tree->getNodes()
    ->addChild((new CElement_Component_TreeView_Node('Images'))
        ->setIcon('fas fa-folder')
        ->addChild('photo.jpg'));
```

---

### Ajax / Lazy Loading

`setNodes(callable $nodes)` switches the tree to ajax mode instead: each time jsTree expands a node, the callback is invoked as `$nodes($parentId, CElement_Component_TreeView_Node $node)` and should push that node's children into `$node` via `addChild()`.

```php
$tree->setNodes(function ($parentId, CElement_Component_TreeView_Node $node) {
    $categories = NestedCategory::query()->where('parent_id', $parentId)->get();
    foreach ($categories as $category) {
        $node->addChild(
            (new CElement_Component_TreeView_Node($category->name))
                ->setId($category->id)
                ->setHasChildren($category->children()->exists())
        );
    }
});
```

Use `setHasChildren(true)` on a child to force jsTree to render an expand arrow before its own children have been loaded yet (e.g. based on a cheap `exists()` check rather than eagerly loading the whole subtree).

If the callback needs classes/files not already autoloaded (it runs through `CAjax::TYPE_TREE_VIEW`/`CAjax_Engine_TreeView` on a fresh request), pass them as the second argument:

```php
$tree->setNodes($callback, app_path('models/NestedCategory.php'));
```

---

### Use Case

Tree views are ideal for file browsers, category hierarchies, menu builders, and any data with parent-child relationships. For drag-and-drop reordering support, consider using the [Nestable](/docs/element/nestable) component instead.
