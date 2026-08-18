# Element - Countdown Timer

The `CElement_Component_CountDownTimer` component renders a countdown (or count-up) timer that updates in real-time on the client side.

Create a countdown timer:

```php
$app = c::app();
$timer = CElement_Component_CountDownTimer::factory();
$timer->setExpiredDate(c::now()->addMinutes(30));
$app->add($timer);

return $app;
```

---

### Expired Date

Set the target date and time for the countdown:

```php
$timer = CElement_Component_CountDownTimer::factory();
$timer->setExpiredDate(c::now()->addHours(2));

// Or with a specific datetime
$timer->setExpiredDate(CCarbon::parse('2025-12-31 23:59:59'));
```

---

### Expired Text

Set the text shown when the countdown reaches zero:

```php
$timer->setExpiredText('Time is up!');
$timer->setExpiredText('Sale has ended');
```

Default: `'Expired'`

---

### Display Format

Customize how the time is displayed using format tokens with `%` prefix:

```php
$timer->setDisplayFormat('%DD:%HH:%mm:%ss');     // default
$timer->setDisplayFormat('%HH hours %mm minutes');
$timer->setDisplayFormat('%mm:%ss');              // minutes and seconds only
```

| Token | Description |
|-------|------------|
| `%D` | Days |
| `%H` | Hours |
| `%m` | Minutes |
| `%s` | Seconds |

---

### Count Up Mode

Switch to count-up mode (elapsed time since a date):

```php
$timer = CElement_Component_CountDownTimer::factory();
$timer->setExpiredDate(CCarbon::parse('2024-01-01 00:00:00'));
$timer->setCountUp();
$timer->setDisplayFormat('%DD days %HH:%mm:%ss');
$app->add($timer);
```

---

### Example: Flash Sale Timer

```php
$widget = $app->addWidget()->setTitle('Flash Sale');
$widget->addDiv()->addClass('text-center p-3')->add(
    CElement_Component_CountDownTimer::factory()
        ->setExpiredDate(c::now()->addHours(6))
        ->setExpiredText('Sale has ended')
        ->setDisplayFormat('%HH:%mm:%ss')
);
```
