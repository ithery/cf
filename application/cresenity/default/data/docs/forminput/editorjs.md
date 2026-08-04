# Form Input - Editor JS

Block-style rich text editor based on Editor.js. Produces clean structured JSON data instead of
raw HTML.

---

### Basic Usage

```php
$form->addField()->setLabel('Content')->addEditorJsControl('content');
```

### Stored value

The control submits JSON rather than HTML. Each block carries its own type and data, so the
content can be rendered differently per medium and queried without parsing markup:

```json
{
  "time": 1735689600000,
  "blocks": [
    { "type": "header", "data": { "text": "Title", "level": 2 } },
    { "type": "paragraph", "data": { "text": "First paragraph." } }
  ],
  "version": "2.28.0"
}
```

Store it in a `text` or `json` column, and decode before rendering:

```php
$content = json_decode($model->content, true);

foreach (carr::get($content, 'blocks', []) as $block) {
    // render per carr::get($block, 'type')
}
```

### Initial block

`setInitialBlock()` sets the block type an empty editor starts with:

```php
$form->addField()->setLabel('Content')->addEditorJsControl('content')
    ->setInitialBlock('header');
```

### Image uploads

Two endpoints handle images, one for uploaded files and one for images added by URL. Both
receive the request and return the JSON payload Editor.js expects:

```php
$form->addField()->setLabel('Content')->addEditorJsControl('content')
    ->setUploadImageByFileEndpoint(curl::base() . 'editor/upload/file')
    ->setUploadImageByUrlEndpoint(curl::base() . 'editor/upload/url');
```

Without these, the image tool has nowhere to send its uploads.

### Available tools

The following tools are bundled and registered automatically:

| Tool | Block |
|---|---|
| `header` | headings |
| `paragraph` | body text |
| `list` | ordered and unordered lists |
| `checklist` | checkable items |
| `table` | tables |
| `image` | images, uploaded or by URL |
| `code` | code blocks |
| `inlineCode` | inline code spans |
| `marker` | highlighted text |
| `link` | link previews |
| `embed` | embedded media |
| `delimiter` | section separators |
| `raw` | raw HTML |

A tool's configuration can be reached through `getTool()`, which returns the tool object or
`null` when the key is unknown:

```php
$control = $form->addField()->setLabel('Content')->addEditorJsControl('content');

$control->getTool('image')->addThumbnail('small', ['width' => 320]);
```

### Summernote

WYSIWYG rich text editor that produces HTML:

```php
$form->addField()->setLabel('Body')->addSummerNoteControl('body');
```

Editor JS is recommended for new projects as it produces structured data. Summernote is
available for cases where raw HTML output is needed.
