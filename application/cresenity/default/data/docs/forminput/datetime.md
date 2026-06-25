# Form Input - Date Time

Enhanced date and time picker controls beyond the standard HTML5 date input.

---

### Date Time Modal

Date and time picker displayed in a modal dialog:

```php
$form->addField()->setLabel('Event Date')->addDateTimeModalControl('event_date');
```

### Date Time Material

Material design styled date-time picker:

```php
$form->addField()->setLabel('Appointment')->addDateTimeMaterialControl('appointment');
```

### Date Range Dropdown

Date range picker with preset options (Today, Last 7 Days, This Month, etc.):

```php
$form->addField()->setLabel('Period')->addDateRangeDropdownButtonControl('period');
```
