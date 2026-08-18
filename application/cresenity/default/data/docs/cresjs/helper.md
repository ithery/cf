# Cres JS - Helper

Utility methods available on the `cresenity` object.

### isJson

Test if a string is valid JSON:

```js
cresenity.isJson('{"name":"John"}');
// true

cresenity.isJson('not json');
// false
```

### toast

Show a toast notification:

```js
cresenity.toast('success', 'Record saved');
cresenity.toast('error', 'Something went wrong');
cresenity.toast('info', 'Loading...');
cresenity.toast('warning', 'Check your input');
```

### message

Show a notification message:

```js
cresenity.message('success', 'Saved successfully');
cresenity.message('error', 'Failed to save');
```

### modal

Open a modal dialog:

```js
cresenity.modal({
    title: 'Edit User',
    reload: {
        url: '/admin/user/edit/1',
    }
});
```

### blockPage / unblockPage

Show/hide a full page loading overlay:

```js
cresenity.blockPage();
// ... do work ...
cresenity.unblockPage();
```

### blockElement / unblockElement

Show/hide a loading overlay on a specific element:

```js
cresenity.blockElement('.my-container');
cresenity.unblockElement('.my-container');
```

### formatCurrency / unformatCurrency

Format and unformat currency values:

```js
cresenity.formatCurrency(1500000);
// '1,500,000'

cresenity.unformatCurrency('1,500,000');
// 1500000
```

### scrollTo

Scroll to an element:

```js
cresenity.scrollTo('#target-element');
```

### value

Get the value of a form element:

```js
let val = cresenity.value('#my-input');
```

### randomGUID

Generate a random GUID:

```js
let guid = cresenity.randomGUID();
// 'a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d'
```
