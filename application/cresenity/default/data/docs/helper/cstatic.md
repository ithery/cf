# Helper cstatic

The `cstatic` helper class returns fixed reference lists that do not come from the database:
months, countries, and HTTP status codes.

### cstatic::month

The `cstatic::month` method returns the months of the year keyed by month number. Names are
translated by default:

```php
$months = cstatic::month();

// [1 => 'Januari', 2 => 'Februari', ...]

$months = cstatic::month(false);

// [1 => 'January', 2 => 'February', ...]
```

The keys are integers from 1 to 12, so the array can be used directly with a month number
taken from a date:

```php
$name = cstatic::month()[(int) date('n')];
```

### cstatic::month_list

The `cstatic::month_list` method is an alias of `cstatic::month`:

```php
$months = cstatic::month_list(true);
```

### cstatic::country

The `cstatic::country` method returns country names keyed by their two-letter ISO 3166-1
code:

```php
$countries = cstatic::country();

// ['AF' => 'Afghanistan', 'AL' => 'Albania', 'DZ' => 'Algeria', ...]
```

The list is suitable for a select input:

```php
$field->addControl('country', 'select')->setDataFromArray(cstatic::country());
```

Country names are not translated.

### cstatic::http_status_header

The `cstatic::http_status_header` method returns the HTTP status codes and their reason
phrases keyed by code:

```php
$statuses = cstatic::http_status_header();

// [200 => 'OK', 404 => 'Not Found', ...]
```
