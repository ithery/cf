# Form Input - File & Image AJAX

AJAX-based file and image upload controls. Files are uploaded immediately without waiting for
form submission, providing instant feedback and preview.

---

### File AJAX

Single file upload via AJAX:

```php
$form->addField()->setLabel('Document')->addFileAjaxControl('document');
```

### Multiple File AJAX

```php
$form->addField()->setLabel('Attachments')->addMultipleFileAjaxControl('attachments');
```

### Image AJAX

Single image upload with preview:

```php
$form->addField()->setLabel('Avatar')->addImageAjaxControl('avatar');
```

### Multiple Image AJAX

```php
$form->addField()->setLabel('Gallery')->addMultipleImageAjaxControl('gallery');
```

## Restricting what may be uploaded

```php
$form->addField()->setLabel('Document')->addFileAjaxControl('document')
    ->setAllowedExtension(['pdf', 'doc', 'docx'])
    ->setMaxUploadSize('5M');
```

- `setAllowedExtension()` — a single extension or an array of them
- `setMaxUploadSize()` — the limit as a number with a unit, for example `5M`

Both are enforced before the file is accepted, so a rejected upload never reaches storage.

### Custom validation

`setValidationCallback()` runs server-side logic against the uploaded file, for rules the two
options above cannot express:

```php
$form->addField()->setLabel('Document')->addFileAjaxControl('document')
    ->setValidationCallback(function ($file) {
        if (/* ... */) {
            throw new Exception('Rejected');
        }
    });
```

The callback is serialised, so it must not capture objects that cannot be serialised.

## Naming and storage

```php
$form->addField()->setLabel('Document')->addFileAjaxControl('document')
    ->setFileName('contract.pdf')
    ->setTempStorage('local-temp');
```

- `setFileName()` — the stored filename, instead of the uploaded one
- `setTempStorage()` — the disk that holds the file between upload and form submission

Because the upload happens before the form is submitted, the file lives in temporary storage
until the form is saved. A form that is abandoned leaves the file there, so the temporary disk
needs its own cleanup schedule.

## Display options

```php
$form->addField()->setLabel('Document')->addFileAjaxControl('document')
    ->setWithInfo(true)
    ->setDisabledUpload(false);
```

- `setWithInfo()` — show file name and size beside the control
- `setDisabledUpload()` — render the control read-only, which suits showing an existing file on
  a form the user may not change

### Image-specific options

`setOnExists()` controls the behaviour when a file of the same name is already present:

```php
$form->addField()->setLabel('Avatar')->addImageAjaxControl('avatar')
    ->setOnExists(true);
```

`withFileProvider()` returns the image file provider, which is where resizing and thumbnail
generation are configured:

```php
$control = $form->addField()->setLabel('Avatar')->addImageAjaxControl('avatar');

$control->withFileProvider();
```
