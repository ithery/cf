# Helper cupload

The `cupload` helper class validates and stores uploaded files, and resolves the per-organisation
upload directory layout.

Every method accepts either an entry from `$_FILES` or the field name to read from it:

```php
cupload::save('avatar');            // reads $_FILES['avatar']
cupload::save($_FILES['avatar']);   // equivalent
```

## Storing files

### cupload::save

The `cupload::save` method moves an uploaded file into the upload directory and returns its
full path, or `false` on failure:

```php
$path = cupload::save('avatar');

$path = cupload::save('avatar', 'profile.png');

$path = cupload::save('avatar', 'profile.png', DOCROOT . 'upload/user/', 0644);
```

When `$filename` is omitted, the original name is used with the current timestamp prepended.
When `$directory` is omitted, the `upload.directory` configuration value is used.

The method throws an exception when the target directory is not writable. It creates the
directory first when `upload.create_directories` is enabled, and replaces spaces in the
filename when `upload.remove_spaces` is enabled.

### cupload::save_array

The `cupload::save_array` method stores a multi-file upload — a single field submitted with
`name="files[]"` — and returns an array of paths:

```php
$paths = cupload::save_array('attachments');
```

## Validation

### cupload::valid

The `cupload::valid` method reports whether the value has the shape of an upload entry, that
is whether the `error`, `name`, `type`, `tmp_name`, and `size` keys are all present:

```php
if (cupload::valid($file)) {
    // ...
}
```

### cupload::required

The `cupload::required` method reports whether a file was actually uploaded and reported no
error:

```php
if (!cupload::required($file)) {
    // no file was submitted
}
```

### cupload::type

The `cupload::type` method reports whether the file matches one of the allowed types. Types
may be given as extensions or MIME types:

```php
if (!cupload::type($file, ['jpg', 'png', 'gif'])) {
    // rejected
}
```

### cupload::size

The `cupload::size` method reports whether the file is within the given size limit. The limit
is written as a number followed by a unit:

```php
if (!cupload::size($file, ['2M'])) {
    // too large
}
```

## Directory layout

Uploads are organised as `upload/{org_code}/{type}/{id}/`. The `$type` argument accepts dot
notation, which creates one directory level per segment:

```php
cupload::create_upload_folder('member.photo', 12);

// upload/{org_code}/member/photo/12/
```

The organisation segment is taken from the current application organisation and is omitted
when there is none.

### cupload::get_upload_path

The `cupload::get_upload_path` method returns the filesystem path for a type and id:

```php
$path = cupload::get_upload_path('member.photo', 12);
```

### cupload::get_upload_src

The `cupload::get_upload_src` method returns the public URL for a stored file:

```php
$src = cupload::get_upload_src('member.photo', 12, 'avatar.png');
```

### cupload::create_upload_folder

The `cupload::create_upload_folder` method creates the directory tree for a type and id,
including every intermediate level:

```php
cupload::create_upload_folder('member.photo', 12);
```

### cupload::delete_all_file

The `cupload::delete_all_file` method removes the named file from the directory belonging to a
type and id:

```php
cupload::delete_all_file('member.photo', 12, 'avatar.png');
```
