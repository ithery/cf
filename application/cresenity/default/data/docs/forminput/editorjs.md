# Form Input - Editor JS

Block-style rich text editor based on Editor.js. Produces clean structured JSON data instead of raw HTML.

---

### Basic Usage

```php
$form->addField()->setLabel('Content')->addEditorJsControl('content');
```

### Summernote

WYSIWYG rich text editor that produces HTML:

```php
$form->addField()->setLabel('Body')->addSummerNoteControl('body');
```

Editor JS is recommended for new projects as it produces structured data. Summernote is available for cases where raw HTML output is needed.
