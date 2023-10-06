# Cres JS - Basic
### Introduction

Secara basic, cresenity mempunyai banyak helper yang bisa digunakan dan bisa langsung digunakan pada project saat penulisan javascript.


untuk alpine object dapat diakses dari `cresenity.Alpine`, semisal untuk mendapatkan version Alpine yang digunakan pada cresenity dapat dicheck dengan perintah berikut:


```js
console.log(cresenity.alpine.Alpine.version);
//3.9.5
```


### Base64


Untuk base64 object dapat diakses melalui cresenity.base64

```js
var foo = 'foo';
var bar = cresenity.base64.encode(foo);

var result = cresenity.base64.decode(bar);

console.log(foo,bar,result);
// foo Zm9v foo
```
### clsx

```js
import clsx from 'clsx';
// or
import { clsx } from 'clsx';

// Strings (variadic)
cres.clsx('foo', true && 'bar', 'baz');
//=> 'foo bar baz'

// Objects
cres.clsx({ foo:true, bar:false, baz:isTrue() });
//=> 'foo baz'

// Objects (variadic)
cres.clsx({ foo:true }, { bar:false }, null, { '--foobar':'hello' });
//=> 'foo --foobar'

// Arrays
cres.clsx(['foo', 0, false, 'bar']);
//=> 'foo bar'

// Arrays (variadic)
cres.clsx(['foo'], ['', 0, false, 'bar'], [['baz', [['hello'], 'there']]]);
//=> 'foo bar baz hello there'

// Kitchen sink (with nesting)
cres.clsx('foo', [1 && 'bar', { baz:false, bat:null }, ['hello', ['world']]], 'cya');
//=> 'foo bar hello world cya'
```

### history
``` javascript
(function(window,undefined){

	// Bind to StateChange Event
	cres.history.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate
		var State = cres.history.getState(); // Note: We are using cres.history.getState() instead of event.state
	});

	// Change our States
	cres.history.pushState({state:1}, "State 1", "?state=1"); // logs {state:1}, "State 1", "?state=1"
	cres.history.pushState({state:2}, "State 2", "?state=2"); // logs {state:2}, "State 2", "?state=2"
	cres.history.replaceState({state:3}, "State 3", "?state=3"); // logs {state:3}, "State 3", "?state=3"
	cres.history.pushState(null, null, "?state=4"); // logs {}, '', "?state=4"
	cres.history.back(); // logs {state:3}, "State 3", "?state=3"
	cres.history.back(); // logs {state:1}, "State 1", "?state=1"
	cres.history.back(); // logs {}, "Home Page", "?"
	cres.history.go(2); // logs {state:3}, "State 3", "?state=3"

})(window);
```
