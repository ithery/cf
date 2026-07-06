# Element - File Manager

The `CElement_Component_FileManager` component renders a web-based file manager — browse, upload, rename, move, preview, download, and delete files — backed by a `CStorage` disk. Rendering/behavior is handled client-side by cres.js, driven server-side by a `CManager_File_Connector_FileManager_FM` instance.

Add one using `addFileManager()`:

```php
$app = c::app();
$fm = $app->addFileManager();
$fm->setDisk('local');
$fm->setRootPath('uploads');

return $app;
```

---

### Disk and Root Path

`setDisk()` picks which `CStorage` disk (see `config/storage.php`) the file manager reads/writes to. `setRootPath()` scopes it to a directory within that disk.

```php
$fm->setDisk('local');      // local filesystem disk
$fm->setRootPath('uploads/networkAccount/' . $accountId);
```

`root_path` is a **container** directory, not the content itself: actual files/folders are expected to live under its `files`/`photos` subfolders (one per category, see below), so the real browsing root ends up being e.g. `uploads/networkAccount/123/files`. Point `root_path` at a folder that either already has those subfolders or where the file manager can create them (uploads auto-create `files`/`photos` as needed).

---

### Picker Mode

Use the file manager as a file picker (e.g. selecting an image for a form field). This enables the "Confirm" action, letting the caller capture the selected file's URL via a callback:

```php
$fm->setAsPicker(true);
```

---

### Theme

```php
$fm->setTheme('bootstrap4');
```

---

### Custom Controller

Override the connector's handler for a given ajax method (e.g. to add custom auth/logic around uploads):

```php
$fm->setController('Item', MyApp_Controller_FileManager_ItemController::class);
```

The method name matches the connector's action names (`Item`, `Folder`, `Upload`, `Download`, `Move`, `DoMove`, `Rename`, `Delete`, `NewFolder`, `Crop`, `CropImage`, `CropNewImage`, `Resize`, `ResizeImage`, `Index`, `Error`) — see `CManager_File_Connector_FileManager::$defaultMethodControllerMapping`.

---

### Custom Configuration

`setConfig()` sets any raw key in the underlying FM config (dot notation supported), e.g. to change folder category settings or size limits:

```php
$fm->setConfig('folder_categories.file.max_size', 100000); // KB
$fm->setConfig('folder_categories.file.valid_mime', ['image/jpeg', 'image/png', 'application/pdf']);
$fm->setConfig('should_create_thumbnails', false);
```

See `system/config/filemanager.php` for the full list of available keys.

---

### Authentication

Require an authenticated session before the file manager's ajax endpoints can be used, even if the app itself doesn't otherwise require auth:

```php
$fm->setEnableAuth(true);
```

This is applied automatically when `c::app()->isAuthEnabled()` is already true.

---

### Available Actions

By default, files can be previewed, downloaded, moved, renamed, and deleted, but not cropped, resized, or "used" (picker-confirmed) — `setAsPicker(true)` is currently the only way to turn on the "use" action.
