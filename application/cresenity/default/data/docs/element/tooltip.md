# Element - Tooltip

The `CElement_Component_Tooltip` component renders a small info icon that shows a tooltip on hover. It uses the Tippy.js library.

Create a tooltip using the factory:

```php
$app = c::app();
$app->addDiv()->add('Hover the icon for more info ')
    ->add(CElement_Component_Tooltip::factory()->setText('This is a helpful tooltip message.'));

return $app;
```

---

### Basic Usage

```php
$tooltip = CElement_Component_Tooltip::factory();
$tooltip->setText('This field is required for all users.');
```

---

### With Form Fields

Tooltips are commonly placed next to form labels to provide additional context:

```php
$field = $form->addField();
$field->setLabel('API Key');
$field->addTextControl('api_key');
$field->add(CElement_Component_Tooltip::factory()->setText('You can find your API key in the developer settings.'));
```

---

### Custom Icon

The default icon is configured via theme (`tooltip.icon`). Override it with `setIcon()`:

```php
$tooltip = CElement_Component_Tooltip::factory();
$tooltip->setIcon('fas fa-question-circle');
$tooltip->setText('Help text here.');
```
