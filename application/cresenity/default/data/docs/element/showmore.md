# Element - ShowMore

The `CElement_Component_ShowMore` component truncates long content and adds a "Show More" / "Show Less" toggle button.

Add a show more element using `addShowMore()`:

```php
$app = c::app();
$app->addShowMore()->setLimit(100)->add(
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit, '
    . 'sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. '
    . 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.'
);

return $app;
```

---

### Character Limit

Set the number of characters to show before truncating:

```php
$showMore = $app->addShowMore();
$showMore->setLimit(50);
$showMore->add('Very long text content...');
```

---

### Usage in Data Table

ShowMore is commonly used inside table column callbacks for displaying long text:

```php
$table->addColumn('description')->setLabel('Description')
    ->setCallback(function ($row, $value) {
        return CElement_Component_ShowMore::factory()
            ->setLimit(100)
            ->add($value);
    });
```

With JSON formatting:

```php
$table->addColumn('payload')->setLabel('Payload')
    ->setCallback(function ($row, $value) {
        return CElement_Component_ShowMore::factory()
            ->addClass('whitespace-pre')
            ->setLimit(200)
            ->add(json_encode(json_decode($value, true), JSON_PRETTY_PRINT));
    });
```
