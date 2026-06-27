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
