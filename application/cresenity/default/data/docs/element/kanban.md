# Element - Kanban

The `CElement_Component_Kanban` component renders a kanban board with draggable cards organized into columns (lists).

Create a kanban board:

```php
$app = c::app();
$kanban = CElement_Component_Kanban::factory();

$todo = $kanban->addList()->setTitle('To Do');
$todo->addItem()->add('Design mockup');
$todo->addItem()->add('Write documentation');

$progress = $kanban->addList()->setTitle('In Progress');
$progress->addItem()->add('Implement API');

$done = $kanban->addList()->setTitle('Done');
$done->addItem()->add('Setup project');

$app->add($kanban);

return $app;
```

---

### Lists

Add columns to the board using `addList()`:

```php
$kanban = CElement_Component_Kanban::factory();
$list = $kanban->addList();
$list->setTitle('Backlog');
```

---

### Save Callback

Handle card reordering with a server-side callback:

```php
$kanban->setSaveCallback(function ($data) {
    // $data contains the new card positions
    // Save to database
});
```

---

### Use Case

Kanban boards are ideal for task management, workflow visualization, and status tracking interfaces where items move between stages.
