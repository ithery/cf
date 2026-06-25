# Element - Data Table

The `CElement_Component_DataTable` is a powerful component for displaying tabular data with built-in support for pagination, sorting, searching, AJAX loading, row actions, and export.

Add a data table to CApp using `addTable()`:

```php
$app = c::app();
$table = $app->addTable();
$table->setDataFromModel(UserModel::class);
$table->addColumn('name')->setLabel('Name');
$table->addColumn('email')->setLabel('Email');

return $app;
```

---

### Data Sources

#### From Model (Recommended)

Load data from an Eloquent-style model with optional query customization:

```php
$table->setDataFromModel(UserModel::class, function (CModel_Query $q) {
    $q->where('status', 'active');
    $q->with(['role']);
    $q->orderBy('created', 'desc');
});

$table->addColumn('name')->setLabel('Name');
$table->addColumn('role.name')->setLabel('Role');
```

Dot notation (`role.name`) accesses related model attributes. Use `$q->with()` for eager loading to avoid N+1 queries.

#### From Model Query

Pass a pre-built query instance:

```php
$query = UserModel::where('status', 'active')->orderBy('name');
$table->setDataFromModelQuery($query);
```

#### From SQL Query

Load data directly from a raw SQL query:

```php
$q = 'SELECT u.*, r.name AS role_name
      FROM users AS u
      INNER JOIN roles AS r ON r.role_id = u.role_id
      WHERE u.status > 0';

$table->setDataFromQuery($q);
$table->addColumn('role_name')->setLabel('Role');
$table->addColumn('username')->setLabel('Username');
```

#### From Array

Load data from a PHP array (not recommended for large datasets):

```php
$table->setDataFromArray([
    ['role' => 'Developer', 'username' => 'albert', 'name' => 'Albert'],
    ['role' => 'QA', 'username' => 'adam', 'name' => 'Adam'],
]);

$table->addColumn('role')->setLabel('Role');
$table->addColumn('username')->setLabel('Username');
$table->addColumn('name')->setLabel('Name');
```

#### From Collection

Load data from a `CCollection`:

```php
$collection = UserModel::all();
$table->setDataFromCollection($collection);
```

#### From Callback

Load data using a custom callback:

```php
$table->setDataFromCallback(function () {
    return UserModel::where('active', true)->get();
});
```

#### From Closure

```php
$table->setDataFromClosure(function () {
    return CDatabase::instance()->query('SELECT * FROM users');
});
```

The default database connection is determined by the `default` key in `config/database.php`. Use `setDomain()` or `setDatabase()` to change it.

---

### Columns

Add columns using `addColumn()`. The argument is the field name (database column or model attribute):

```php
$table->addColumn('name')->setLabel('Name');
$table->addColumn('email')->setLabel('Email');
$table->addColumn('created')->setLabel('Registered');
```

#### Column Options

| Method | Description |
|--------|------------|
| `setLabel($label)` | Column header text |
| `setWidth($width)` | Column width (e.g. `'200px'`, `'20%'`) |
| `setAlign($align)` | Text alignment: `'left'`, `'center'`, `'right'` |
| `setAlignCenter()` | Shorthand for center alignment |
| `setAlignRight()` | Shorthand for right alignment |
| `setSortable($bool)` | Enable/disable sorting on this column |
| `setSearchable($bool)` | Enable/disable searching on this column |
| `setVisible($bool)` | Show/hide column |
| `setInvisible()` | Hide the column |
| `setNoLineBreak($bool)` | Prevent text wrapping |
| `setNoWrap($bool)` | Prevent text wrapping |
| `setDataType($type)` | Column data type |
| `setFormat($format)` | Display format |
| `addClass($class)` | Add CSS class to column cells |

#### Column Callback

Transform the displayed value using a callback. The callback receives the full row data and the column value:

```php
$table->addColumn('status')->setLabel('Status')->setCallback(function ($row, $value) {
    if ($value == 'active') {
        return '<span class="badge badge-success">Active</span>';
    }
    return '<span class="badge badge-danger">Inactive</span>';
});
```

```php
$table->addColumn('amount')->setLabel('Amount')->setCallback(function ($row, $value) {
    return 'Rp ' . number_format($value, 0, ',', '.');
});
```

```php
$table->addColumn('request')->setLabel('Request')->setCallback(function ($row, $value) {
    return CElement_Component_ShowMore::factory()
        ->addClass('whitespace-pre')
        ->add(json_encode(json_decode($value, true), JSON_PRETTY_PRINT));
});
```

#### Search and Sort Callbacks

Customize how searching and sorting behave for a column:

```php
$table->addColumn('full_name')->setLabel('Name')
    ->setSearchCallback(function ($query, $search) {
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%");
        });
    })
    ->setSortCallback(function ($query, $direction) {
        $query->orderBy('first_name', $direction);
    });
```

#### Export Callback

Customize the value when exporting to Excel:

```php
$table->addColumn('status')->setLabel('Status')
    ->setCallback(function ($row, $value) {
        return '<span class="badge">' . $value . '</span>';
    })
    ->setExportCallback(function ($row, $value) {
        return $value;
    });
```

---

### Row Actions

Add action buttons to each row (edit, delete, view, etc.):

```php
$table->addRowAction()
    ->setIcon('ti ti-pencil')
    ->setLabel('Edit')
    ->setLink(c::url('admin/user/edit/{user_id}'));

$table->addRowAction()
    ->setIcon('ti ti-trash')
    ->setLabel('Delete')
    ->setConfirm(true)
    ->setLink(c::url('admin/user/delete/{user_id}'));
```

Use `{column_name}` placeholders in the URL — they are replaced with the row's column value.

#### Dynamic Row Actions

Use `withRowCallback` to change the action based on row data:

```php
$table->addRowAction()->withRowCallback(function ($element, $row) {
    $isActive = carr::get($row, 'is_active');
    $element->setIcon($isActive ? 'ti ti-close' : 'ti ti-check')
        ->setLabel($isActive ? 'Deactivate' : 'Activate')
        ->setLink(c::url('admin/user/toggle/{user_id}'));
});
```

#### Conditional Row Actions

Show or hide actions based on conditions:

```php
$table->addRowAction()->withRowCallback(function ($element, $row) {
    $isLocked = carr::get($row, 'is_locked');
    if ($isLocked) {
        $element->setVisibility(false);
    } else {
        $element->setIcon('ti ti-trash')
            ->setLabel('Delete')
            ->setConfirm(true)
            ->setLink(c::url('admin/user/delete/{user_id}'));
    }
});
```

#### Action Location

Control where the action column appears:

```php
$table->setActionLocation('right');  // default
$table->setActionLocation('left');
```

#### Action Header Label

```php
$table->setActionHeaderLabel('Actions');
```

---

### Table Options

#### Display and Pagination

| Method | Description |
|--------|------------|
| `setDisplayLength($n)` | Rows per page (default: 10) |
| `setPagingList([10, 25, 50, 100])` | Page size options in dropdown |
| `setLabelNoData($label)` | Text shown when table is empty |
| `setInfoText($text)` | Custom info text below the table |

```php
$table->setDisplayLength(25);
$table->setPagingList([10, 25, 50, 100]);
$table->setLabelNoData('No records found');
```

#### AJAX Loading

```php
$table->setAjax(true);             // Enable AJAX data loading
$table->setAjaxMethod('POST');     // HTTP method for AJAX requests (default: GET)
```

#### Layout and Appearance

| Method | Description |
|--------|------------|
| `setTableStriped($bool)` | Striped row styling |
| `setTableBordered($bool)` | Add borders |
| `setResponsive($bool)` | Enable responsive layout |
| `setScrollX($bool)` | Enable horizontal scrolling |
| `setScrollY($bool)` | Enable vertical scrolling |
| `setShowHeader($bool)` | Show/hide table header |
| `setHeaderNoLineBreak($bool)` | Prevent header text wrapping |
| `setWidgetTitle($bool)` | Show title in widget wrapper |
| `setHeaderSortable($bool)` | Enable/disable column reordering by drag |

#### Advanced Options

| Method | Description |
|--------|------------|
| `setDom($dom)` | Custom DataTables DOM layout string |
| `setColReorder($bool)` | Enable column reordering |
| `setFixedColumn($n)` | Fix the first N columns |
| `setFixedHeader($bool)` | Fix the header when scrolling |
| `setNumbering($bool)` | Show row numbers |
| `setGroupBy($column)` | Group rows by column value |
| `setKey($fieldname)` | Set the primary key field |
| `setAutoRefresh($seconds)` | Auto-refresh data every N seconds |
| `setCustomColumnHeader($html)` | Custom HTML for column headers |
| `setOption($key, $val)` | Set any DataTables option directly |

#### Database Connection

```php
$table->setDomain('other-domain.com');           // Use a different domain's config
$table->setDatabase($dbInstance);                 // Use a specific database instance
$table->setDatabaseResolver($resolverCallback);  // Custom database resolver
```

---

### Row Selection

Enable row selection checkboxes (for bulk operations):

```php
$table->haveRowSelection();
```

---

### Row Class Callback

Dynamically add CSS classes to rows:

```php
$table->setRowClassCallback(function ($row) {
    if (carr::get($row, 'status') === 'overdue') {
        return 'table-danger';
    }
    return '';
});
```

---

### Export

Export the table data to Excel:

```php
$table->downloadExcel('users.xlsx');
```

Or queue the export for large datasets:

```php
$table->queueDownloadExcel('exports/users.xlsx', 'local');
```

---

### Translations

Table UI labels (pagination, search, etc.) can be customized by overriding the translation file. Copy from `system/i18n/en_US/element/datatable.php` to your application's `i18n/` directory.

```php
<?php
return [
    'emptyTable' => 'No data available in table',
    'info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
    'infoEmpty' => 'Showing 0 to 0 of 0 entries',
    'infoFiltered' => '(filtered from _MAX_ total entries)',
    'lengthMenu' => 'Show _MENU_ entries',
    'loadingRecords' => 'Loading...',
    'processing' => 'Processing...',
    'search' => 'Search',
    'zeroRecords' => 'No matching records found',
    'paginate' => [
        'first' => 'First',
        'last' => 'Last',
        'next' => 'Next',
        'previous' => 'Previous',
    ],
];
```

See the [DataTables language documentation](https://datatables.net/plug-ins/i18n/English.html) for all available keys.
