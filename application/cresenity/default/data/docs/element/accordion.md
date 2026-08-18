# Element - Accordion

The `CElement_Component_Accordion` component renders a collapsible accordion with expandable/collapsible items.

Add an accordion to CApp using `addAccordion()`:

```php
$app = c::app();
$accordion = $app->addAccordion();

$item1 = $accordion->addItem();
$item1->setTitle('Section 1');
$item1->add('Content for section 1');

$item2 = $accordion->addItem();
$item2->setTitle('Section 2');
$item2->add('Content for section 2');

return $app;
```

---

### Accordion Items

Each item has a title (header) and content area. Add any element inside an item:

```php
$accordion = $app->addAccordion();

$item = $accordion->addItem();
$item->setTitle('User Details');
$item->addField()->setLabel('Name')->addTextControl('name');
$item->addField()->setLabel('Email')->addEmailControl('email');
```

---

### Active Item

Set an item to be expanded by default:

```php
$item = $accordion->addItem();
$item->setTitle('Open by Default');
$item->setActive(true);
$item->add('This section is expanded on load.');
```

---

### Dynamic Content

Build accordion from data:

```php
$accordion = $app->addAccordion();

$faqs = [
    ['q' => 'How to reset password?', 'a' => 'Go to Settings > Security.'],
    ['q' => 'How to change email?', 'a' => 'Go to Settings > Profile.'],
    ['q' => 'How to delete account?', 'a' => 'Contact support.'],
];

foreach ($faqs as $i => $faq) {
    $item = $accordion->addItem();
    $item->setTitle($faq['q']);
    $item->setActive($i === 0);
    $item->addP()->add($faq['a']);
}
```
