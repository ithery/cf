# Form Input - Select Search

AJAX-powered searchable select input using Select2. For use inside Repeater or dynamic containers, use [SelectTwo](/docs/forminput/selectsearch) instead.

---

### Basic Usage

```php
$form->addField()->setLabel('User')->addSelectSearchControl('userId')
    ->setDataFromModel(UserModel::class)
    ->setKeyField('user_id')
    ->setSearchField('username')
    ->setValue($userId);
```

### Data Sources

#### From Model

```php
$form->addField()->setLabel('Country')->addSelectSearchControl('country')
    ->setDataFromModel(CountryModel::class, function ($q) {
        $q->where('active', true);
        $q->orderBy('name');
    })
    ->setKeyField('country_id')
    ->setSearchField('name');
```

#### From SQL Query

```php
$form->addField()->setLabel('Product')->addSelectSearchControl('product')
    ->setQuery('SELECT * FROM products WHERE status > 0')
    ->setKeyField('product_id')
    ->setSearchField('name');
```

#### From Collection

```php
$collection = ProductModel::where('active', true)->get();
$form->addField()->setLabel('Product')->addSelectSearchControl('product')
    ->setDataFromCollection($collection)
    ->setKeyField('id')
    ->setSearchField('name');
```

---

### Format

#### String Template

Use `{field}` placeholders to build the display template:

```php
$select->setFormat('<div>{name} <span class="badge badge-success">{code}</span></div>');
```

Set different templates for dropdown and selected display:

```php
$select->setFormatResult('<div>{name}<br/><small>{email}</small></div>');
$select->setFormatSelection('{name}');
```

#### Closure Format

Use a closure for complex formatting with access to the model:

```php
$select->setFormat(function ($model) {
    $div = c::div();
    $div->addDiv()->add($model->name);
    $div->addSpan()->addClass('badge badge-info')->add($model->code);
    return $div;
});
```

---

### Multiple Selection

```php
$select->setMultiple(true);
$select->setName('countries[]');
$select->setValue(['ID', 'US', 'JP']);
```

---

### Prepend Data

Add static options at the top of the results (shown on page 1):

```php
$select->prependRow(['id' => 'ALL', 'name' => '-- All --']);
```

Or set all prepend data at once:

```php
$select->setPrependData([
    ['id' => 'ALL', 'name' => '-- All --'],
    ['id' => 'NONE', 'name' => '-- None --'],
]);
```

---

### Options

| Method | Description |
|--------|------------|
| `setKeyField($field)` | Primary key field name |
| `setSearchField($field)` | Field(s) to search on |
| `setSearchFullTextField($field)` | Full-text search field(s) |
| `setMinInputLength($n)` | Minimum characters before search triggers |
| `setDelay($ms)` | Debounce delay in milliseconds (default: 100) |
| `setPerPage($n)` | Results per AJAX page (default: 10) |
| `setAllowClear($bool)` | Show clear button |
| `setAutoSelect($bool)` | Auto-select first result |
| `setPlaceholder($text)` | Placeholder text |
| `setOnModal($bool)` | Fix z-index for use inside Bootstrap modals |
| `addDropdownClass($class)` | Add CSS class to dropdown |

---

### Depends On

Filter options based on another field's value:

```php
$continentSelect = $form->addField()->setLabel('Continent')->addSelectControl('continent')
    ->setList($continentList);

$countrySelect = $form->addField()->setLabel('Country')->addSelectSearchControl('country')
    ->setDataFromModel(CountryModel::class)
    ->setKeyField('code')
    ->setSearchField('name');
$countrySelect->setDependsOn($continentSelect, function ($q, $value) {
    if (strlen($value) > 0) {
        $q->where('continent', '=', $value);
    }
});
```

---

### Extending SelectSearch

Create a reusable custom select by extending the class:

```php
class MyApp_FormInput_UserSelect extends CElement_FormInput_SelectSearch {
    protected function build() {
        parent::build();
        $this->setPlaceholder('Search user...');
        $this->setDataFromModel(UserModel::class);
        $this->setKeyField('user_id');
        $this->setSearchField(['username', 'email']);
        $this->setFormat('<div>{username} <small class="text-muted">{email}</small></div>');
    }
}
```

---

### Value Callback

Transform field values in the response:

```php
$select->setValueCallback(function ($row, $key, $value) {
    if ($key === 'name') {
        return strtoupper($value);
    }
    return $value;
});
```
