# Helper cdbutils

The `cdbutils` helper class runs short queries and reads database schema information without
going through a query builder or model.

## Deprecated query methods

Five methods on this class are deprecated in favour of the equivalents on the connection
object:

| Deprecated | Use instead |
|---|---|
| `cdbutils::get_value()` | `c::db()->getValue()` |
| `cdbutils::get_row()` | `c::db()->getRow()` |
| `cdbutils::get_array()` | `c::db()->getArray()` |
| `cdbutils::get_list()` | `c::db()->getList()` |
| `cdbutils::row_exists()` | — no replacement, build the query directly |

They are documented below because existing code calls them, and their return shapes are not
obvious from their names.

## Queries

### cdbutils::get_value

The `cdbutils::get_value` method returns the first column of the first row:

```php
$count = cdbutils::get_value('SELECT COUNT(*) FROM users WHERE status > 0');
```

A connection may be passed as the second argument. When omitted, `c::db()` is used. This
applies to every method on this page.

```php
$count = cdbutils::get_value($sql, $db);
```

### cdbutils::get_array

The `cdbutils::get_array` method returns the first column of every row as a flat array:

```php
$ids = cdbutils::get_array('SELECT user_id FROM users WHERE status > 0');

// [1, 2, 3]
```

### cdbutils::get_list

The `cdbutils::get_list` method returns the first two columns of every row as a key/value
array, which suits select inputs:

```php
$options = cdbutils::get_list('SELECT user_id, name FROM users ORDER BY name');

// [1 => 'Alice', 2 => 'Bob']
```

### cdbutils::get_row

The `cdbutils::get_row` method returns the first row of the result as an object, or `null`
when the query matches nothing:

```php
$user = cdbutils::get_row('SELECT * FROM users WHERE user_id = 1');

// $user->name
```

### cdbutils::get_row_count_from_base_query

The `cdbutils::get_row_count_from_base_query` method returns the number of rows a query would
produce, without fetching them:

```php
$total = cdbutils::get_row_count_from_base_query($sql);
```

### cdbutils::row_exists

The `cdbutils::row_exists` method reports whether a row matching the given conditions exists:

```php
$exists = cdbutils::row_exists('users', ['email' => 'a@example.com']);
```

### cdbutils::load_sql

The `cdbutils::load_sql` method executes a SQL script consisting of multiple statements:

```php
cdbutils::load_sql($sql);
```

## Schema

### cdbutils::table_exists

```php
$exists = cdbutils::table_exists('users');
```

### cdbutils::get_table_list

The `cdbutils::get_table_list` method returns the names of every table in the database:

```php
$tables = cdbutils::get_table_list();
```

### cdbutils::get_table_count

```php
$count = cdbutils::get_table_count();
```

### cdbutils::get_table_info

The `cdbutils::get_table_info` method returns engine, collation, and row count information for
every table:

```php
$info = cdbutils::get_table_info();
```

### cdbutils::get_column_info

The `cdbutils::get_column_info` method returns column definitions for a table:

```php
$columns = cdbutils::get_column_info('users');
```

### cdbutils::empty_table

The `cdbutils::empty_table` method removes every row from a table:

```php
cdbutils::empty_table('log_activity');
```

## Maintenance

### cdbutils::convert_table_engine

The `cdbutils::convert_table_engine` method converts every table to the given storage engine:

```php
cdbutils::convert_table_engine('InnoDB');
```

### cdbutils::convert_table_charset

The `cdbutils::convert_table_charset` method converts every table to the given character set
and collation:

```php
cdbutils::convert_table_charset('utf8mb4', 'utf8mb4_unicode_ci');
```

Both methods operate on every table in the database and are intended for migration scripts
rather than request handling.

## Escaping

The methods on this page accept raw SQL strings. Values interpolated into those strings must
be escaped by the caller:

```php
$sql = 'SELECT * FROM users WHERE email = ' . $db->escape($email);
```

Prefer the query builder or a model when the query is built from user input.
