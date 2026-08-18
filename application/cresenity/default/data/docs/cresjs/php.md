# Cres JS - PHP Functions

JavaScript implementations of common PHP functions, available under `cresenity.php`.

### echo

Write to the browser console:

```js
cresenity.php.echo('hello world');
// hello world
```

### ucfirst

Capitalize the first character of a string:

```js
cresenity.php.ucfirst('hello world');
// Hello world
```

[PHP Documentation](https://www.php.net/manual/en/function.ucfirst.php)

### strtotime

Parse an English date/time string into a Unix timestamp:

```js
cresenity.php.strtotime('now');
cresenity.php.strtotime('10 September 2000');
cresenity.php.strtotime('+1 day');
cresenity.php.strtotime('+1 week');
cresenity.php.strtotime('+1 week 2 days 4 hours 2 seconds');
cresenity.php.strtotime('next Thursday');
cresenity.php.strtotime('last Monday');
```

[PHP Documentation](https://www.php.net/manual/en/function.strtotime.php)

### is_numeric

Check if a value is numeric or a numeric string:

```js
cresenity.php.is_numeric('42');     // true
cresenity.php.is_numeric(1337);    // true
cresenity.php.is_numeric(9.1);     // true
cresenity.php.is_numeric('hello'); // false
```

[PHP Documentation](https://www.php.net/manual/en/function.is-numeric.php)
