# Element - Image

The `CElement_Component_Image` component renders an image with optional progressive loading support. When progressive loading is enabled, a low-quality thumbnail is shown first and replaced with the full image when it finishes loading.

Add an image component using `addImage()`:

```php
$app = c::app();
$image = $app->addImage();
$image->setSrc(c::media('img/photo.jpg'));
$image->setAlt('Photo');

return $app;
```

---

### Basic Usage

```php
$image = $app->addImage();
$image->setSrc(c::url('uploads/product/photo.jpg'));
$image->setAlt('Product Photo');
$image->addClass('img-fluid rounded');
```

---

### Progressive Loading

Enable progressive image loading by setting a low-quality thumbnail. The thumbnail is displayed immediately with a blur effect, then replaced by the full-resolution image once loaded:

```php
$image = $app->addImage();
$image->setSrc(c::url('uploads/photo-full.jpg'));
$image->setProgressiveThumbnail(c::url('uploads/photo-thumb.jpg'));
$image->setAlt('Profile Photo');
```

This improves perceived loading performance for large images.

---

### Image Options

| Method | Description |
|--------|------------|
| `setSrc($src)` | Full-resolution image URL |
| `setAlt($alt)` | Alt text for accessibility |
| `setProgressiveThumbnail($url)` | Low-quality thumbnail URL for progressive loading |

---

### Difference from Standard Img Element

| `addImg()` | `addImage()` |
|-----------|-------------|
| Raw `<img>` HTML element | Component with progressive loading support |
| No wrapper element | Wrapped in a container div |
| Basic attributes only | Supports `setProgressiveThumbnail()` |

Use `addImg()` for simple inline images. Use `addImage()` when you need progressive loading.
