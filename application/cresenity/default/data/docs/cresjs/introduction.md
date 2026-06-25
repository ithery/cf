# Cres JS - Introduction

### Cresenity Javascript Object

CF includes a JavaScript object called `cresenity` that is automatically available in all CApp pages. It provides utilities for AJAX operations, UI management, modals, confirmations, and more.

The configuration is based on your application's `app.php` config, exposed as a global `capp` variable in JavaScript.

```js
console.log(capp.baseUrl);       // base URL
console.log(capp.environment);   // 'development' or 'production'
console.log(capp.debug);         // true or false
```

### Alpine.js

Cresenity automatically loads Alpine.js, which can be used directly in your views. For documentation, see [Alpine JS Website](https://alpinejs.dev/).

### Events

The `cresenity` object dispatches events through the window:

```js
window.addEventListener('cresenity:loaded', () => {
    // cresenity object is ready
});

cresenity.on('reload:success', (event) => {
    // handle custom event
});
```

### Available Properties

| Property | Description |
|----------|------------|
| `cresenity.url` | URL utility object |
| `cresenity.ui` | UI utilities |
| `cresenity.php` | PHP-equivalent JS functions |
| `cresenity.base64` | Base64 encode/decode |
| `cresenity.alpine` | Alpine.js instance |
| `cresenity.history` | History/state management |
| `cresenity.sse` | Server-Sent Events |
| `cresenity.theme` | Theme management |
| `cresenity.formatter` | Number/date formatting |
| `cresenity.observer` | DOM mutation observers |
| `cresenity.clsx` | CSS class name builder |
| `cresenity.dateFns` | date-fns library |
| `cresenity.collect` | collect.js library |
| `cresenity.version` | Cres JS version string |
