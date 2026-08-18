# Cres JS - Basic

Cresenity provides several built-in utilities accessible directly from the `cresenity` object.

### Base64

Encode and decode Base64 strings:

```js
let encoded = cresenity.base64.encode('hello');
// aGVsbG8=

let decoded = cresenity.base64.decode(encoded);
// hello
```

### clsx

Build CSS class strings conditionally:

```js
cresenity.clsx('foo', true && 'bar', 'baz');
// 'foo bar baz'

cresenity.clsx({ foo: true, bar: false, baz: true });
// 'foo baz'

cresenity.clsx(['foo', 0, false, 'bar']);
// 'foo bar'
```

### History

Manage browser history state:

```js
cresenity.history.pushState({ page: 1 }, 'Page 1', '?page=1');
cresenity.history.replaceState({ page: 2 }, 'Page 2', '?page=2');
cresenity.history.back();
cresenity.history.go(2);

cresenity.history.Adapter.bind(window, 'statechange', function () {
    let state = cresenity.history.getState();
});
```

### date-fns

The [date-fns](https://date-fns.org/) library is available for date manipulation:

```js
let now = new Date();
let formatted = cresenity.dateFns.format(now, 'yyyy-MM-dd');
let tomorrow = cresenity.dateFns.addDays(now, 1);
```

### collect.js

The [collect.js](https://collect.js.org/) library is available for collection manipulation:

```js
let items = cresenity.collect([1, 2, 3, 4, 5]);
let sum = items.sum();
let filtered = items.filter(i => i > 2).all();
```

### Debounce

Debounce a function call:

```js
let handler = cresenity.debounce(() => {
    console.log('debounced');
}, 300);
```

### Reactive (Alpine)

Create reactive data with Alpine.js:

```js
let data = cresenity.reactive({ count: 0 }, (data) => {
    console.log('count changed:', data.count);
});
data.count++;
```
