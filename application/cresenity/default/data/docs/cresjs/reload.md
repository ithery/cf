# Cres JS - Reload

Load or refresh content in a DOM element via AJAX.

```js
cresenity.reload(options);
```

### Options

| Option | Default | Description |
|--------|---------|------------|
| `url` | `'/'` | AJAX URL to load content from (required) |
| `selector` | - | CSS selector of the target element (required) |
| `method` | `'get'` | HTTP method |
| `reloadType` | `'reload'` | How to insert content (see below) |
| `dataAddition` | `{}` | Additional data to send with the request |
| `onBlock` | `false` | Callback before AJAX starts (for custom loading) |
| `onUnblock` | `false` | Callback after AJAX completes |
| `blockHtml` | `false` | Custom loading indicator HTML |
| `onSuccess` | `false` | Callback on success with response data |
| `onComplete` | `false` | Callback when AJAX completes |

### Reload Types

| Type | Description |
|------|------------|
| `reload` | Replace content inside the target element |
| `after` | Insert after the target element |
| `before` | Insert before the target element |
| `append` | Append inside the target element |
| `prepend` | Prepend inside the target element |

### Shorthand Methods

```js
cresenity.append(options);   // reloadType: 'append'
cresenity.prepend(options);  // reloadType: 'prepend'
cresenity.after(options);    // reloadType: 'after'
cresenity.before(options);   // reloadType: 'before'
```

### Events

```js
// Success
cresenity.on('reload:success', (event) => {
    const response = event.detail;
});

// Error
cresenity.on('reload:error', (event) => {
    const { xhr, ajaxOptions, error } = event.detail;
});

// Complete
cresenity.on('reload:complete', (event) => {
    // no parameters
});
```

Events can also be listened before cresenity loads:

```js
window.addEventListener('cresenity:reload:success', (event) => {
    const response = event.detail;
});
```
