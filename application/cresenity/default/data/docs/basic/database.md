# Database

CF uses `CModel` (an Eloquent-based ORM) for database operations. This page covers table naming conventions, primary keys, default columns, and soft deletes.

---

### Table Naming

Tables use **snake_case** singular names. The model class name maps to the table name by convention:

| Model Class | Table Name |
|---|---|
| `CApp_Model_User` | `user` |
| `NWModel_Character` | `character` |
| `NWModel_CharacterJob` | `character_job` |

You can override the table name explicitly:

```php
class NWModel_Character extends CModel {
    protected $table = 'character';
}
```

---

### Primary Key

The primary key defaults to `{table_name}_id`. This is **auto-derived** from the table name — you do not need to set it manually.

| Table | Primary Key |
|---|---|
| `character` | `character_id` |
| `character_job` | `character_job_id` |
| `item` | `item_id` |
| `map_gate` | `map_gate_id` |

The framework resolves this in `CModel`:

```php
// CModel automatically sets:
// $this->primaryKey = $this->table . '_id'
```

Override with `$primaryKey` if the column name differs:

```php
class MyModel extends CModel {
    protected $table = 'my_table';
    protected $primaryKey = 'id'; // if not using {table}_id convention
}
```

The primary key column should be `bigint(20) unsigned AUTO_INCREMENT`.

---

### Default Columns

Every table should include these standard columns. CF handles them automatically through model events and soft delete traits.

#### Timestamp Columns

| Column | Type | Description |
|---|---|---|
| `created` | `datetime NULL` | When the record was created. Set automatically by the framework. |
| `createdby` | `varchar(50) NULL` | Username/identifier of who created the record. |
| `updated` | `datetime NULL` | When the record was last updated. Set automatically on save. |
| `updatedby` | `varchar(50) NULL` | Username/identifier of who last updated the record. |

CF uses `created`/`updated` instead of Laravel's `created_at`/`updated_at`. These are defined as constants in `CModel`:

```php
class CModel {
    const CREATED = 'created';
    const UPDATED = 'updated';
    const CREATEDBY = 'createdby';
    const UPDATEDBY = 'updatedby';
}
```

#### Soft Delete Columns

| Column | Type | Description |
|---|---|---|
| `deleted` | `datetime NULL` | When the record was soft-deleted. |
| `deletedby` | `varchar(50) NULL` | Username/identifier of who deleted the record. |

#### Status Column

| Column | Type | Description |
|---|---|---|
| `status` | `int(11) NOT NULL DEFAULT 1` | Active/inactive flag. `1` = active, `0` = soft-deleted. |

The `status` column is the core of CF's soft delete system. When a model uses `CModel_SoftDelete_SoftDeleteTrait`, the framework **automatically adds a global scope** that filters all queries to only return rows where `status > 0`.

```php
// This global scope is added automatically:
$builder->where('status', '>', 0);
```

This means:
- **`Model::all()`** only returns active records (`status > 0`)
- **`Model::find($id)`** will return `null` if the record has `status = 0`
- **`$model->delete()`** sets `status = 0` (soft delete), does NOT remove the row
- **`Model::withTrashed()`** includes soft-deleted records
- **`Model::onlyTrashed()`** returns only soft-deleted records
- **`$model->restore()`** sets `status = 1`
- **`$model->forceDelete()`** permanently removes the row

**Important:** If your table has a `status` column, you must account for this automatic filtering. New tables that don't set `status = 1` as default will have their rows invisible to queries. Always use `NOT NULL DEFAULT 1` for the status column.

---

### Complete Table Template

Here is a typical `CREATE TABLE` statement following all conventions:

```sql
CREATE TABLE character_job (
    character_job_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    org_id bigint(20) unsigned NOT NULL DEFAULT 0,
    character_id bigint(20) unsigned NOT NULL,
    job_id bigint(20) unsigned NOT NULL,
    level int(11) NOT NULL DEFAULT 1,
    exp int(11) NOT NULL DEFAULT 0,
    created datetime DEFAULT NULL,
    createdby varchar(50) DEFAULT NULL,
    updated datetime DEFAULT NULL,
    updatedby varchar(50) DEFAULT NULL,
    deleted datetime DEFAULT NULL,
    deletedby varchar(50) DEFAULT NULL,
    status int(11) NOT NULL DEFAULT 1,
    PRIMARY KEY (character_job_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

### Guarded & Fillable

Use `$guarded` to prevent mass assignment of the primary key:

```php
class NWModel_CharacterJob extends CModel {
    protected $table = 'character_job';
    protected $guarded = ['character_job_id'];
}
```

All other columns are mass-assignable by default. You can also use `$fillable` for a whitelist approach instead.

---

### Model Relationships

Define relationships using the same conventions:

```php
class NWModel_Character extends CModel {
    protected $table = 'character';
    protected $guarded = ['character_id'];

    // belongsTo: FK column defaults to {related_table}_id
    public function currentMap() {
        return $this->belongsTo(NWModel_Map::class, 'current_map_id', 'map_id');
    }

    // hasMany: FK column defaults to {this_table}_id
    public function inventory() {
        return $this->hasMany(NWModel_Inventory::class);
    }

    // belongsToMany: pivot table name is specified explicitly
    public function skill() {
        return $this->belongsToMany(NWModel_Skill::class, 'character_skill')
            ->withPivot(['level']);
    }
}
```

---

### org_id Column

Multi-tenant applications use `org_id` (`bigint unsigned NOT NULL DEFAULT 0`) to scope data per organization. This is not enforced by the framework but is a strong convention — include it in every table that stores tenant-specific data.

---

### Array-Backed Models (No Database Required)

`CModel_ArrayDriver_ArrayDriverTrait` lets a `CModel` run against a real,
queryable SQLite database that's created and cached automatically - no
`config/database.php` connection, no manual migration, no real database
server needed at all. Useful for demo/seed data, fixtures, and small
reference/lookup tables that don't deserve a spot in the app's main schema.

Give it either seed rows or just a column schema:

```php
class MyApp_Model_Country extends CModel {
    use CModel_ArrayDriver_ArrayDriverTrait;

    protected $rows = [
        ['country_id' => 1, 'name' => 'Indonesia', 'code' => 'ID'],
        ['country_id' => 2, 'name' => 'Singapore', 'code' => 'SG'],
    ];
}
```

or, if you don't have fixed data to seed (the table is meant to be written to
at runtime, e.g. by a demo/example feature):

```php
class MyApp_Model_Widget extends CModel {
    use CModel_ArrayDriver_ArrayDriverTrait;

    protected $schema = [
        'name' => 'string',
        'quantity' => 'integer',
        'is_active' => 'boolean',
    ];
}
```

Column types map directly to `CDatabase_Schema_Blueprint` method names
(`string`, `integer`, `boolean`, `dateTime`, `text`, ...). The primary key
(`{table}_id`, per the convention above) is added automatically and does not
need to appear in `$rows`/`$schema`.

Behind the scenes (`CModel_ArrayDriver_ArrayDriverTrait::bootArrayDriverTrait()`):

- On boot, it builds/reuses a per-model SQLite file at
  `DOCROOT/temp/model/array/cache/{kebab-class-name}.sqlite`.
- The cache is keyed by the **source file's modification time** - edit
  `$rows`/`$schema` and the cache is detected as stale and rebuilt
  automatically on the next request. No manual cache-busting needed.
- If the cache directory isn't writable, it transparently falls back to an
  in-memory SQLite connection (`:memory:`) instead - still fully functional,
  just not persisted/shared across requests.
- Relations (`belongsTo`/`hasMany`/etc.) work normally even between two
  different array-backed models, since each resolves its own isolated
  connection independently (`getConnectionName()` returns the model's own
  class name) - CF just issues separate queries per model, same as it would
  for models on genuinely different database connections.

This is exactly how the framework's own docs examples stay runnable without
a real database - see `application/cresenity/default/libraries/Cresenity/Demo/Model/Item.php`
for a `$rows`-seeded example, and `Cresenity/Demo/Api/Model/*` (covered in
[API - OAuth2](/docs/api/oauth)) for a `$schema`-only example backing a full
OAuth2 authorization server.
