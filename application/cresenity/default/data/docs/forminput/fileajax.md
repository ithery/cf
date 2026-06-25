# Form Input - File & Image AJAX

AJAX-based file and image upload controls. Files are uploaded immediately without waiting for form submission, providing instant feedback and preview.

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
