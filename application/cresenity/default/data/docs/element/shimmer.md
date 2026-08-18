# Element - Shimmer

The `CElement_Component_Shimmer` component renders a loading skeleton placeholder — animated shapes that mimic the layout of content being loaded.

Add a shimmer to CApp using `addShimmer()`:

```php
$app = c::app();
$app->addShimmer()->withBuilder(function (CElement_Component_Shimmer_Builder $b) {
    $b->col('col-12', function ($b) {
        $b->img()->row('', function ($b) {
            $b->col('col-6 big')
                ->col('col-4 empty big')
                ->col('col-2 big');
        });
    });
});

return $app;
```

---

### Builder

The shimmer component uses a builder pattern to define the skeleton layout. The builder provides methods to compose rows, columns, and image placeholders:

#### Columns

Use `col()` to define column-based layouts. The first argument is the CSS class (Bootstrap grid), the second is an optional callback for nested content:

```php
$b->col('col-12', function ($b) {
    // nested content
});

$b->col('col-6 big');    // big height placeholder
$b->col('col-4 empty');  // empty/invisible placeholder (spacing)
$b->col('col-12');       // normal height placeholder
```

#### Rows

Use `row()` to create a horizontal row of columns:

```php
$b->row('', function ($b) {
    $b->col('col-6 big')
        ->col('col-4 empty big')
        ->col('col-2 big');
});
```

#### Image Placeholder

Use `img()` to add a square image placeholder:

```php
$b->img();
```

---

### Full Example

A card-like shimmer with image and text placeholders:

```php
$app->addDiv()->addClass('row')
    ->addDiv()->addClass('col-md-6')
    ->addShimmer()->withBuilder(function (CElement_Component_Shimmer_Builder $b) {
        $b->col('col-12', function ($b) {
            $b->img()
                ->row('', function ($b) {
                    $b->col('col-6 big')
                        ->col('col-4 empty big')
                        ->col('col-2 big')
                        ->col('col-4')
                        ->col('col-8 empty')
                        ->col('col-6')
                        ->col('col-6 empty')
                        ->col('col-12 empty');
                });
        });
    });
```

---

### Use Case

Shimmer placeholders are typically used as loading indicators while content is being fetched via AJAX. Place a shimmer inside a container, then replace it with real content when the AJAX request completes.
