# Cres JS - Basic
### Introduction

Secara basic, cresenity mempunyai banyak helper yang bisa digunakan dan bisa langsung digunakan pada project saat penulisan javascript.


untuk alpine object dapat diakses dari `cresenity.Alpine`, semisal untuk mendapatkan version Alpine yang digunakan pada cresenity dapat dicheck dengan perintah berikut:


```js
console.log(cresenity.Alpine.version);
//3.5.1
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
