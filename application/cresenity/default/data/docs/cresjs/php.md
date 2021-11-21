# Cres JS - PHPJS Function
### Introduction

Keseluruhan fungsi berada dalam scope `cresenity.php`



### echo

Fungsi echo untuk menulis ke javascript console

```js
cresenity.php.echo('hello world');
// hello world
```

### ucfirst

Membuat karakter pertama dari string menjadi uppercase

[PHP Documentation](https://www.php.net/manual/en/function.ucfirst.php)

```js
cresenity.php.ucfirst('hello world');
// Hello world
```

### strtotime

Parsing string dengan english language ke unix timestamp

[PHP Documentation](https://www.php.net/manual/en/function.strtotime.php)

```js
cresenity.php.strtotime('now');
cresenity.php.strtotime('10 September 2000');
cresenity.php.strtotime('+1 day');
cresenity.php.strtotime('+1 week');
cresenity.php.strtotime('+1 week 2 days 4 hours 2 seconds');
cresenity.php.strtotime('next Thursday');
cresenity.php.strtotime('last Monday');
cresenity.php.strtotime('2021-1-01 22:33');
```

### is_numeric

Melakukan check apakah variabel adalah angka atau string numerik

[PHP Documentation](https://www.php.net/manual/en/function.is-numeric.php)

```js
cresenity.php.is_numeric('42');
//true
cresenity.php.is_numeric(1337);
//true
cresenity.php.is_numeric(0x539);
//true
cresenity.php.is_numeric(1337e0);
//true
cresenity.php.is_numeric('0x539');
//true
cresenity.php.is_numeric(9.1);
//true
```