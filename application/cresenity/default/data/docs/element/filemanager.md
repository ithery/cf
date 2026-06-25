# Element - File Manager

The `CElement_Component_FileManager` component renders a web-based file manager interface for browsing, uploading, and managing files on the server.

Add a file manager using `addFileManager()`:

```php
$app = c::app();
$fm = $app->addFileManager();
$fm->setDisk('local');
$fm->setRootPath('/uploads');

return $app;
```

---

### Disk Configuration

Set which storage disk the file manager operates on:

```php
$fm->setDisk('local');      // local filesystem
$fm->setDisk('s3');         // Amazon S3
$fm->setDisk('local-temp'); // temporary storage
```

Disks are configured in `config/storage.php`.

---

### Root Path

Set the root directory that the file manager displays:

```php
$fm->setRootPath('/uploads/images');
```

---

### Picker Mode

Use the file manager as a file picker (e.g. for selecting images in a form):

```php
$fm->setAsPicker(true);
```

---

### Theme

```php
$fm->setTheme('bootstrap4');
```

---

### Custom Configuration

Pass additional configuration options:

```php
$fm->setConfig('maxUploadSize', 10 * 1024 * 1024); // 10MB
$fm->setConfig('allowedExtensions', ['jpg', 'png', 'gif', 'pdf']);
```

---

### Authentication

Enable authentication for the file manager:

```php
$fm->setEnableAuth(true);
```

---

### Custom Controller

Override the default file manager controller methods:

```php
$fm->setController('upload', MyApp_Controller_FileManager::class);
```
