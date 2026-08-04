# PHPCF - Model

The commands on this page inspect models and their tables. All of them are run from inside the
application folder, since the models they read belong to that application.

```
cd application/ohayomart
phpcf model:list
```

### model:list

Lists every model registered in the application.

```
phpcf model:list
```

### model:show

Shows the details of a model: its table name, connection, columns with their types, and the
relations it declares.

```
phpcf model:show {model}
```

The model name may be given with or without the application prefix:

```
phpcf model:show Product
phpcf model:show OHModel_Product
```

Options:

- `--database=` — the connection to use, when the model does not use the default one
- `--json` — output as JSON instead of a table, for consumption by other scripts

```
phpcf model:show Product --json
```

The recognised relations are `hasOne`, `hasMany`, `hasOneThrough`, `hasManyThrough`,
`belongsTo`, `belongsToMany`, `morphOne`, `morphTo`, `morphMany`, `morphToMany`, and
`morphedByMany`.

### model:tables

Lists the tables that have a model. Useful for finding tables that do **not** yet have one —
compare its output against the table list from the database commands.

```
phpcf model:tables
```

### model:update

Updates the property annotations in a model file so they match the columns of its table. Run
it after a schema change, so the `@property` docblocks reflect the database again and static
analysis and IDE completion stay correct.

```
phpcf model:update {table}
```

```
phpcf model:update product
```

Only the annotation block is rewritten; the code inside the class is left untouched.

## Scout

Available when the application uses search indexing.

### model:scout:flush

Removes every record of a model from the search index.

```
phpcf model:scout:flush {model}
```

### model:scout:delete-all-indexes

Deletes all indexes.

```
phpcf model:scout:delete-all-indexes
```
