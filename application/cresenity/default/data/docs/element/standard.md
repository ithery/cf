# Element - Standard Element

Standard elements are the basic HTML building blocks available in CApp. Each element maps directly to an HTML tag and can be added to any parent element (including `CApp` itself).

All standard elements share a common set of methods inherited from `CElement`, plus tag-specific methods where applicable.

---

### Available Elements

| Method | HTML Tag | Class |
|--------|----------|-------|
| `addDiv()` | `<div>` | `CElement_Element_Div` |
| `addSpan()` | `<span>` | `CElement_Element_Span` |
| `addP()` | `<p>` | `CElement_Element_P` |
| `addA()` | `<a>` | `CElement_Element_A` |
| `addButton()` | `<button>` | `CElement_Element_Button` |
| `addImg()` | `<img>` | `CElement_Element_Img` |
| `addLabel()` | `<label>` | `CElement_Element_Label` |
| `addH1()` | `<h1>` | `CElement_Element_H1` |
| `addH2()` | `<h2>` | `CElement_Element_H2` |
| `addH3()` | `<h3>` | `CElement_Element_H3` |
| `addH4()` | `<h4>` | `CElement_Element_H4` |
| `addH5()` | `<h5>` | `CElement_Element_H5` |
| `addH6()` | `<h6>` | `CElement_Element_H6` |
| `addUl()` | `<ul>` | `CElement_Element_Ul` |
| `addOl()` | `<ol>` | `CElement_Element_Ol` |
| `addLi()` | `<li>` | `CElement_Element_Li` |
| `addTable()` | `<table>` | `CElement_Element_Table` |
| `addTr()` | `<tr>` | `CElement_Element_Tr` |
| `addTd()` | `<td>` | `CElement_Element_Td` |
| `addPre()` | `<pre>` | `CElement_Element_Pre` |
| `addCode()` | `<code>` | `CElement_Element_Code` |
| `addBlockquote()` | `<blockquote>` | `CElement_Element_Blockquote` |
| `addIframe()` | `<iframe>` | `CElement_Element_Iframe` |
| `addCanvas()` | `<canvas>` | `CElement_Element_Canvas` |
| `addImg()` | `<img>` | `CElement_Element_Img` |
| `addBr()` | `<br>` | `CElement_Element_Br` |
| `addHr()` | `<hr>` | `CElement_Element_Hr` |
| `addKbd()` | `<kbd>` | `CElement_Element_Kbd` |

Every `addXxx()` method accepts an optional `$id` parameter. If omitted, a unique ID is auto-generated.

---

### Common Methods

All standard elements inherit these methods from `CElement`:

#### Content

```php
$div = $app->addDiv();
$div->add('Text or HTML content');
$div->add($anotherElement);
```

#### CSS Classes

```php
$div->addClass('container mt-3 text-center');
$div->removeClass('mt-3');
$classes = $div->getClasses();
```

#### Attributes

```php
$div->setAttr('data-id', '42');
$div->setAttr('style', 'color: red;');
$div->removeAttr('style');
$value = $div->getAttr('data-id');

// Set multiple attributes from array
$div->setAttrFromArray([
    'data-id' => '42',
    'data-name' => 'test',
]);
```

#### Custom CSS

```php
$div->customCss('background-color', '#f5f5f5');
$div->customCss('padding', '20px');
```

#### Visibility

```php
$div->setVisibility(false);  // hide element
$div->setVisibility(true);   // show element
```

---

### Container Elements

#### Div

The most commonly used container element:

```php
$div = $app->addDiv('my-container');
$div->addClass('card p-3');
$div->add('Content inside the div');
```

#### Span

Inline container:

```php
$span = $app->addSpan();
$span->addClass('badge badge-primary');
$span->add('New');
```

#### Paragraph

```php
$p = $app->addP();
$p->add('This is a paragraph of text.');
```

#### Blockquote

```php
$quote = $app->addBlockquote();
$quote->addClass('blockquote');
$quote->add('To be or not to be.');
```

---

### Link and Button

#### Anchor (A)

```php
$a = $app->addA();
$a->setHref(c::url('user/profile'));
$a->setTarget('_blank');
$a->addClass('btn btn-primary');
$a->add('View Profile');
```

Specific methods:

| Method | Description |
|--------|------------|
| `setHref($url)` | Set the `href` attribute |
| `setTarget($target)` | Set the `target` attribute (e.g. `_blank`) |
| `setTargetBlank()` | Shorthand for `setTarget('_blank')` |

#### Button

```php
$btn = $app->addButton();
$btn->addClass('btn btn-success');
$btn->setAttr('type', 'submit');
$btn->add('Save');
```

---

### Headings

```php
$app->addH1()->add('Main Title');
$app->addH2()->add('Section Title');
$app->addH3()->add('Subsection');
$app->addH4()->add('Sub-subsection');
$app->addH5()->add('Minor Heading');
$app->addH6()->add('Smallest Heading');
```

---

### Lists

#### Unordered List

```php
$ul = $app->addUl();
$ul->addClass('list-group');
$ul->addLi()->add('First item');
$ul->addLi()->add('Second item');
$ul->addLi()->add('Third item');
```

#### Ordered List

```php
$ol = $app->addOl();
$ol->addLi()->add('Step one');
$ol->addLi()->add('Step two');
$ol->addLi()->add('Step three');
```

---

### Table

Build an HTML table structure manually:

```php
$table = $app->addTable();
$table->addClass('table table-striped');

$tr = $table->addTr();
$tr->addTd()->add('Name');
$tr->addTd()->add('Email');

$tr2 = $table->addTr();
$tr2->addTd()->add('John');
$tr2->addTd()->add('john@example.com');
```

> **Note:** This is the raw HTML `<table>` element. For data tables with sorting, pagination, and AJAX support, use the `CElement_Component_DataTable` component instead. See [Element - Table](/docs/element/table).

---

### Image

```php
$img = $app->addImg();
$img->setSrc(c::media('img/logo.png'));
$img->setAlt('Company Logo');
$img->addClass('img-fluid');
```

Specific methods:

| Method | Description |
|--------|------------|
| `setSrc($src)` | Set the `src` attribute |
| `setAlt($alt)` | Set the `alt` attribute |

---

### Iframe

```php
$iframe = $app->addIframe();
$iframe->setSrc('https://example.com');
$iframe->setAttr('width', '100%');
$iframe->setAttr('height', '500');
$iframe->setAttr('frameborder', '0');
```

---

### Code and Preformatted

```php
// Inline code
$code = $app->addCode();
$code->add('$variable = "hello";');

// Preformatted block
$pre = $app->addPre();
$pre->add('Line 1\nLine 2\nLine 3');

// Combined
$pre = $app->addPre();
$code = $pre->addCode();
$code->add(htmlspecialchars('<?php echo "Hello"; ?>'));
```

#### Keyboard Input

```php
$app->addKbd()->add('Ctrl+C');
```

---

### Canvas

```php
$canvas = $app->addCanvas('my-chart');
$canvas->setAttr('width', '400');
$canvas->setAttr('height', '300');
```

---

### Self-Closing Elements

These elements don't have content:

```php
// Line break
$app->addBr();

// Horizontal rule
$app->addHr();
```

---

### Label

```php
$label = $app->addLabel();
$label->setAttr('for', 'email-input');
$label->add('Email Address');
```

---

### Nesting Elements

All elements can contain child elements, allowing you to build complex layouts:

```php
$card = $app->addDiv()->addClass('card');
$header = $card->addDiv()->addClass('card-header');
$header->addH5()->add('Card Title');

$body = $card->addDiv()->addClass('card-body');
$body->addP()->add('Card content goes here.');

$footer = $card->addDiv()->addClass('card-footer');
$link = $footer->addA();
$link->setHref('#');
$link->addClass('btn btn-sm btn-primary');
$link->add('Read More');
```

---

### Dynamic Element Creation

You can also create elements dynamically by tag name using `addElement()`:

```php
$element = $app->addElement('div', 'my-id');
$element->addClass('custom');
$element->add('Dynamic element');
```

This is useful when the element type is determined at runtime.
