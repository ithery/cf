# Form Input - Map Picker

Map-based location picker for selecting geographic coordinates.

---

### Basic Usage

```php
$form->addField()->setLabel('Location')->addMapPickerControl('location');
```

### Setting the initial position

`setValue()` takes latitude and longitude as a single comma-separated string, which is also the
format the control submits:

```php
$form->addField()->setLabel('Location')->addMapPickerControl('location')
    ->setValue('-6.200000,106.816666');
```

### Radius

A radius circle can be drawn around the marker, which suits delivery areas and coverage zones:

```php
$form->addField()->setLabel('Coverage')->addMapPickerControl('location')
    ->setRadius(500);
```

`radius()` is an alias of `setRadius()`.

### Map behaviour

```php
$form->addField()->setLabel('Location')->addMapPickerControl('location')
    ->setDraggable(true)
    ->setScrollwheel(false)
    ->markerInCenter();
```

- `setDraggable()` — whether the map itself can be panned
- `setScrollwheel()` — whether the scroll wheel zooms the map; turning this off prevents the
  page from trapping the wheel while scrolling past the map
- `markerInCenter()` — keep the marker pinned to the centre of the map, so panning the map
  moves the selected point

### Writing into other fields

Rather than reading the combined value, the control can write each part into separate inputs.
Each selector accepts an element or a CSS selector:

```php
$latitude = $form->addField()->setLabel('Latitude')->addTextControl('latitude');
$longitude = $form->addField()->setLabel('Longitude')->addTextControl('longitude');

$form->addField()->setLabel('Location')->addMapPickerControl('location')
    ->setLatitudeSelector($latitude)
    ->setLongitudeSelector($longitude);
```

`setRadiusSelector()` and `setSearchSelector()` work the same way, binding the radius value and
the search box to elements defined elsewhere on the form.

### Reacting to changes

`setJsOnChanged()` runs JavaScript whenever the selected point changes:

```php
$form->addField()->setLabel('Location')->addMapPickerControl('location')
    ->setJsOnChanged('console.log(lat, lng);');
```

### Reading the value

```php
$post = $_POST;
list($latitude, $longitude) = explode(',', carr::get($post, 'location'));
```

When no point has been selected the value is `0,0`, so treat that as unset rather than as a
location off the coast of Africa.
