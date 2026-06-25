# Element - Tab

The `CElement_List_TabList` component provides a tabbed interface for organizing content into switchable panels.

Add a tab list to CApp using `addTabList()`:

```php
$app = c::app();
$tabList = $app->addTabList();
$tabList->addTab()->setLabel('Tab 1')->addDiv()->add('Content 1');
$tabList->addTab()->setLabel('Tab 2')->addDiv()->add('Content 2');

return $app;
```

---

### Tab Position

Control where the tab headers appear:

```php
$tabList = $app->addTabList()->setTabPosition('top');    // default
$tabList = $app->addTabList()->setTabPosition('left');
// or use shorthand
$tabList = $app->addTabList()->setTabPositionTop();
$tabList = $app->addTabList()->setTabPositionLeft();
```

---

### Adding Tabs

Each tab has a label and content. You can add any element inside a tab:

```php
$tabList = $app->addTabList()->setTabPosition('top');

$tab1 = $tabList->addTab();
$tab1->setLabel('Profile');
$tab1->addField()->setLabel('Name')->addTextControl('name');
$tab1->addField()->setLabel('Email')->addEmailControl('email');

$tab2 = $tabList->addTab();
$tab2->setLabel('Settings');
$tab2->addField()->setLabel('Theme')->addSelectControl('theme')
    ->setList(['light' => 'Light', 'dark' => 'Dark']);
```

---

### Tab with Icon

```php
$tab = $tabList->addTab();
$tab->setLabel('Activity');
$tab->setIcon('ti ti-timer');
```

---

### Active Tab

Set which tab is active by default:

```php
$tab1 = $tabList->addTab()->setLabel('First');
$tab2 = $tabList->addTab()->setLabel('Second')->setActive(true);
```

Or set active tab by ID:

```php
$tabList->setActiveTab('my-tab-id');
```

---

### AJAX Tabs

Load tab content via AJAX when the tab is clicked, instead of rendering all tabs upfront:

```php
$tabList = $app->addTabList()->setTabPosition('top');

$tab1 = $tabList->addTab()->setLabel('Activity')
    ->setAjaxUrl(c::url('admin/user/tab/activity/' . $userId))
    ->setActive(true);
$tab1->setNoPadding()->setIcon('ti ti-timer');

$tab2 = $tabList->addTab()->setLabel('Settings')
    ->setAjaxUrl(c::url('admin/user/tab/settings/' . $userId));
$tab2->setIcon('ti ti-settings');
```

Enable AJAX for the entire tab list:

```php
$tabList->setAjax(true);
```

---

### No Padding

Remove the default padding from a tab's content area:

```php
$tab->setNoPadding(true);
```

---

### Widget Class

Add CSS classes to the tab widget wrapper:

```php
$tabList->addWidgetClass('shadow-sm');
```
