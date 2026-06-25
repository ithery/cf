# Element - Gallery

The `CElement_Component_Gallery` component renders a lightbox-enabled image gallery. Clicking a thumbnail opens the full-size image in a modal viewer with navigation between images.

Add a gallery using `addElement('gallery')` or via the factory:

```php
$app = c::app();
$gallery = $app->addElement('gallery');

$gallery->addItem()->setSrc(c::url('uploads/photo1.jpg'));
$gallery->addItem()->setSrc(c::url('uploads/photo2.jpg'));
$gallery->addItem()->setSrc(c::url('uploads/photo3.jpg'));

return $app;
```

---

### Gallery Items

Each item represents a single image in the gallery. Add items using `addItem()`:

```php
$gallery = $app->addElement('gallery');

$item = $gallery->addItem();
$item->setSrc(c::url('uploads/full-size.jpg'));
$item->setThumbnail(c::url('uploads/thumbnail.jpg'));
```

| Method | Description |
|--------|------------|
| `setSrc($url)` | Full-size image URL (shown in lightbox) |
| `setThumbnail($url)` | Thumbnail URL (shown in grid). Falls back to `src` if not set |

---

### Custom Thumbnail Styling

Use `withImageCallback` to customize the thumbnail `<img>` element:

```php
$item = $gallery->addItem();
$item->setSrc(c::url('uploads/photo.jpg'));
$item->setThumbnail(c::url('uploads/photo-thumb.jpg'));
$item->withImageCallback(function ($img) {
    $img->addClass('rounded shadow-sm');
    $img->setAttr('style', 'width: 150px; height: 150px; object-fit: cover;');
});
```

---

### Gallery from Data

Build a gallery dynamically from a collection or array:

```php
$gallery = $app->addElement('gallery');
$gallery->addClass('row');

$photos = PhotoModel::where('album_id', $albumId)->get();

foreach ($photos as $photo) {
    $item = $gallery->addItem();
    $item->setSrc($photo->getFullUrl());
    $item->setThumbnail($photo->getThumbnailUrl());
    $item->withImageCallback(function ($img) {
        $img->addClass('col-md-3 p-1 img-fluid');
    });
}
```

---

### Layout

The gallery renders items as inline elements. Use CSS classes to control the grid layout:

```php
$gallery = $app->addElement('gallery');
$gallery->addClass('d-flex flex-wrap gap-2');

foreach ($images as $src) {
    $item = $gallery->addItem();
    $item->setSrc($src);
    $item->withImageCallback(function ($img) {
        $img->setAttr('style', 'width: 200px; height: 200px; object-fit: cover;');
        $img->addClass('rounded');
    });
}
```
