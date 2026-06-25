# Form Input - Select Two

Select2-based searchable select with cres.js auto-initialization. Unlike SelectSearch, this component works correctly inside dynamic containers like Repeater because initialization is handled by cres.js via MutationObserver.

---

### Basic Usage

```php
$form->addField()->setLabel('Country')->addSelectTwoControl('country')
    ->setDataFromModel(CountryModel::class)
    ->setKeyField('code')
    ->setSearchField('name');
```

### Data Sources

```php
// From model
$select->setDataFromModel(CountryModel::class, function ($q) {
    $q->where('active', true);
});

// From SQL query
$select->setQuery('SELECT * FROM countries WHERE active = 1');

// From collection
$select->setDataFromCollection(CountryModel::all());

// From closure
$select->setDataFromClosure(function () {
    return CountryModel::where('active', true)->get();
});
```

### Format

```php
$select->setFormat('<div>{name} <span class="badge badge-info">{code}</span></div>');
```

### Multiple Selection

```php
$select->setMultiple(true);
$select->setName('countries[]');
$select->setValue(['ID', 'US', 'JP']);
```

### Prepend Data

```php
$select->setPrependData([
    ['code' => 'ALL', 'name' => '-- All Countries --'],
]);
```

### Depends On

```php
$select->setDependsOn($continentSelect, function ($q, $value) {
    if (strlen($value) > 0) {
        $q->where('continent', '=', $value);
    }
});
```

### Options

| Method | Description |
|--------|------------|
| `setKeyField($field)` | Primary key field |
| `setSearchField($field)` | Search field(s) |
| `setMinInputLength($n)` | Minimum characters before search |
| `setDelay($ms)` | Debounce delay (default: 100) |
| `setPerPage($n)` | Results per page (default: 10) |
| `setAllowClear($bool)` | Show clear button |
| `setPlaceholder($text)` | Placeholder text |

See [Select Search](/docs/forminput/selectsearch) for shared API details.
