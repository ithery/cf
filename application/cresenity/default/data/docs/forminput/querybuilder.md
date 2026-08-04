# Form Input - Query Builder

Visual rule builder for constructing filter conditions. The user assembles groups of rules
through dropdowns, and the control submits them as JSON that can be applied to a model query.

---

### Basic Usage

```php
$form->addField()->setLabel('Filter')->addQueryBuilderControl('filter');
```

An empty builder offers no fields to filter on, so a filter definition is normally supplied at
the same time.

### Defining the filters

`withFilterBuilder()` receives a builder object and defines which fields may be filtered:

```php
$form->addField()->setLabel('Filter')->addQueryBuilderControl('filter')
    ->withFilterBuilder(function ($builder) {
        $builder->withFilter(function ($filter) {
            $filter->setId('name')->setLabel('Name')->setTypeString();
        });

        $builder->withFilter(function ($filter) {
            $filter->setId('age')->setLabel('Age')->setTypeInteger();
        });

        $builder->withFilter(function ($filter) {
            $filter->setId('created')->setLabel('Created')->setTypeDate();
        });
    });
```

`addFilter()` is the imperative equivalent, returning the filter for direct configuration:

```php
$builder->addFilter('name')->setLabel('Name')->setTypeString();
```

### Filter types

| Method | Type |
|---|---|
| `setTypeString()` | text |
| `setTypeInteger()` | whole numbers |
| `setTypeDouble()` | decimal numbers |
| `setTypeDate()` | date |
| `setTypeTime()` | time |
| `setTypeDatetime()` | date and time |
| `setTypeBoolean()` | true/false |

`setType()` accepts the type name directly when it is computed rather than fixed.

### Filter options

```php
$filter->setId('status')
    ->setLabel('Status')
    ->setTypeString()
    ->setMultiple()
    ->setPlaceholder('Choose a status')
    ->setValidation(['min' => 1]);
```

- `setMultiple()` — allow several values to be selected for one rule
- `setPlaceholder()` — placeholder for the value input
- `setValidation()` — validation rules applied to the value

### Control options

```php
$form->addField()->setLabel('Filter')->addQueryBuilderControl('filter')
    ->setName('filter_rules')
    ->setApplySelect2(true)
    ->withToastOnError(true);
```

- `setName()` — the submitted field name, when it should differ from the control id
- `setApplySelect2()` — initialise select2 on the rule value selects, which helps when a filter
  has many options
- `withToastOnError()` — show a toast when the submitted rules cannot be parsed, instead of
  failing silently

### Applying the rules to a query

`CElement_FormInput_QueryBuilder_Parser` translates the submitted JSON into query conditions:

```php
$post = $_POST;

$query = OHModel_Product::query();
$parser = new CElement_FormInput_QueryBuilder_Parser($query, ['name', 'age', 'created']);
$query = $parser->parse(carr::get($post, 'filter'));

$result = $query->get();
```

The second constructor argument whitelists the fields that may be filtered. Fields outside the
list are ignored, so a rule referring to an unexpected column cannot reach the database.

### Custom handling for a field

`addRuleCallback()` takes over a single field, which is needed when a rule maps to something
other than a plain column — a relation, a computed value, or a differently named column:

```php
$parser->addRuleCallback('customer_name', function ($query, $rule) {
    return $query->whereHas('customer', function ($q) use ($rule) {
        $q->where('name', 'like', '%' . carr::get($rule, 'value') . '%');
    });
});
```
