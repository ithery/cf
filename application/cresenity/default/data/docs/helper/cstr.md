# Helper cstr


### cstr::after

The `cstr::after` method returns everything after the given value in a string. The entire string will be returned if the value does not exist within the string:
```php
$slice = cstr::after('This is my name', 'This is');

// ' my name'
```

### cstr::afterLast

The `cstr::afterLast` method returns everything after the last occurrence of the given value in a string. The entire string will be returned if the value does not exist within the string:
```php

    $slice = cstr::afterLast('Controller_HomeController', '_');

    // 'HomeController'
```

### cstr::ascii

The `cstr::ascii` method will attempt to transliterate the string into an ASCII value:
```php
    $slice = cstr::ascii('û');

    // 'u'
```

### cstr::before

The `cstr::before` method returns everything before the given value in a string:
```php
    $slice = cstr::before('This is my name', 'my name');

    // 'This is '
```

### cstr::beforeLast

The `cstr::beforeLast` method returns everything before the last occurrence of the given value in a string:
```php
    $slice = cstr::beforeLast('This is my name', 'is');

    // 'This '
```

### cstr::between

The `cstr::between` method returns the portion of a string between two values:
```php

    $slice = cstr::between('This is my name', 'This', 'name');

    // ' is my '
```

### cstr::betweenFirst

The `cstr::betweenFirst` method returns the smallest possible portion of a string between two values:
```php
    $slice = cstr::betweenFirst('[a] bc [d]', '[', ']');

    // 'a'
```

### cstr::camel

The `cstr::camel` method converts the given string to `camelCase`:
```php
    $converted = cstr::camel('foo_bar');

    // fooBar
```

### cstr::contains

The `cstr::contains` method determines if the given string contains the given value. This method is case sensitive:
```php
$contains = cstr::contains('This is my name', 'my');

// true
```
You may also pass an array of values to determine if the given string contains any of the values in the array:
```php
$contains = cstr::contains('This is my name', ['my', 'foo']);

// true
```

### cstr::containsAll

The `cstr::containsAll` method determines if the given string contains all of the values in a given array:
```php
$containsAll = cstr::containsAll('This is my name', ['my', 'name']);

// true
```

### cstr::endsWith

The `cstr::endsWith` method determines if the given string ends with the given value:
```php
    $result = cstr::endsWith('This is my name', 'name');

    // true
```

You may also pass an array of values to determine if the given string ends with any of the values in the array:
```php
$result = cstr::endsWith('This is my name', ['name', 'foo']);

// true

$result = cstr::endsWith('This is my name', ['this', 'foo']);

// false
```

### cstr::excerpt

The `cstr::excerpt` method extracts an excerpt from a given string that matches the first instance of a phrase within that string:
```php
$excerpt = cstr::excerpt('This is my name', 'my', [
    'radius' => 3
]);

// '...is my na...'
```
The `radius` option, which defaults to `100`, allows you to define the number of characters that should appear on each side of the truncated string.

In addition, you may use the `omission` option to define the string that will be prepended and appended to the truncated string:
```php
    $excerpt = cstr::excerpt('This is my name', 'name', [
        'radius' => 3,
        'omission' => '(...) '
    ]);

    // '(...) my name'
```

### cstr::finish

The `cstr::finish` method adds a single instance of the given value to a string if it does not already end with that value:
```php
    $adjusted = cstr::finish('this/string', '/');

    // this/string/

    $adjusted = cstr::finish('this/string/', '/');

    // this/string/
```
### cstr::headline

The `cstr::headline` method will convert strings delimited by casing, hyphens, or underscores into a space delimited string with each word's first letter capitalized:
```php
    $headline = cstr::headline('steve_jobs');

    // Steve Jobs

    $headline = cstr::headline('EmailNotificationSent');

    // Email Notification Sent
```

#### cstr::inlineMarkdown

The `cstr::inlineMarkdown` method converts GitHub flavored Markdown into inline HTML using [CommonMark](https://commonmark.thephpleague.com/). However, unlike the `markdown` method, it does not wrap all generated HTML in a block-level element:
```php
    $html = cstr::inlineMarkdown('**Cresenity**');

    // <strong>Cresenity</strong>
```
### cstr::is

The `cstr::is` method determines if a given string matches a given pattern. Asterisks may be used as wildcard values:
```php
    $matches = cstr::is('foo*', 'foobar');

    // true

    $matches = cstr::is('baz*', 'foobar');

    // false
```
### cstr::isAscii()

The `cstr::isAscii` method determines if a given string is 7 bit ASCII:
```php
$isAscii = cstr::isAscii('Taylor');

// true

$isAscii = cstr::isAscii('ü');

// false
```
### cstr::isJson

The `cstr::isJson` method determines if the given string is valid JSON:
```php
    $result = cstr::isJson('[1,2,3]');

    // true

    $result = cstr::isJson('{"first": "John", "last": "Doe"}');

    // true

    $result = cstr::isJson('{first: "John", last: "Doe"}');

    // false
```
### cstr::isUuid

The `cstr::isUuid` method determines if the given string is a valid UUID:
```php
    $isUuid = cstr::isUuid('a0a2a2d2-0b87-4a18-83f2-2529882be2de');

    // true

    $isUuid = cstr::isUuid('laravel');

    // false
```

### cstr::kebab

The `cstr::kebab` method converts the given string to `kebab-case`:
```php
    $converted = cstr::kebab('fooBar');

    // foo-bar
```
### cstr::lcfirst

The `cstr::lcfirst` method returns the given string with the first character lowercased:
```php
    $string = cstr::lcfirst('Foo Bar');

    // foo Bar
```
### cstr::length

The `cstr::length` method returns the length of the given string:
```php
    $length = cstr::length('Cresenity');

    // 10
```
### cstr::limit

The `cstr::limit` method truncates the given string to the specified length:
```php
    $truncated = cstr::limit('The quick brown fox jumps over the lazy dog', 20);

    // The quick brown fox...
```
You may pass a third argument to the method to change the string that will be appended to the end of the truncated string:
```php
    $truncated = cstr::limit('The quick brown fox jumps over the lazy dog', 20, ' (...)');

    // The quick brown fox (...)
```

### cstr::lower

The `cstr::lower` method converts the given string to lowercase:
```php
    $converted = cstr::lower('CRESENITY');

    // cresenity
```
### cstr::markdown

The `cstr::markdown` method converts GitHub flavored Markdown into HTML using [CommonMark](https://commonmark.thephpleague.com/):
```php
    $html = cstr::markdown('# Cresenity');

    // <h1>Cresenity</h1>

    $html = cstr::markdown('# Cresenity <b>Framework</b>', [
        'html_input' => 'strip',
    ]);

    // <h1>Cresenity Framework</h1>
```
### cstr::mask

The `cstr::mask` method masks a portion of a string with a repeated character, and may be used to obfuscate segments of strings such as email addresses and phone numbers:
```php
    $string = cstr::mask('myname@example.com', '*', 3);

    // myn***************
```
If needed, you provide a negative number as the third argument to the `mask` method, which will instruct the method to begin masking at the given distance from the end of the string:
```php
    $string = cstr::mask('myname@example.com', '*', -15, 3);

    // myn***@example.com
```
### cstr::orderedUuid

The `cstr::orderedUuid` method generates a "timestamp first" UUID that may be efficiently stored in an indexed database column. Each UUID that is generated using this method will be sorted after UUIDs previously generated using the method:
```php
    return (string) cstr::orderedUuid();
```

### cstr::padBoth

The `cstr::padBoth` method wraps PHP's `str_pad` function, padding both sides of a string with another string until the final string reaches a desired length:
```php
    $padded = cstr::padBoth('James', 10, '_');

    // '__James___'

    $padded = cstr::padBoth('James', 10);

    // '  James   '
```
### cstr::padLeft

The `cstr::padLeft` method wraps PHP's `str_pad` function, padding the left side of a string with another string until the final string reaches a desired length:
```php
    $padded = cstr::padLeft('James', 10, '-=');

    // '-=-=-James'

    $padded = cstr::padLeft('James', 10);

    // '     James'
```
### cstr::padRight

The `cstr::padRight` method wraps PHP's `str_pad` function, padding the right side of a string with another string until the final string reaches a desired length:
```php
    $padded = cstr::padRight('James', 10, '-');

    // 'James-----'

    $padded = cstr::padRight('James', 10);

    // 'James     '
```
### cstr::plural

The `cstr::plural` method converts a singular word string to its plural form. This function supports any of the languages support by CF's pluralizer
```php
    $plural = cstr::plural('car');

    // cars

    $plural = cstr::plural('child');

    // children
```
You may provide an integer as a second argument to the function to retrieve the singular or plural form of the string:
```php
    $plural = cstr::plural('child', 2);

    // children

    $singular = cstr::plural('child', 1);

    // child
```
### cstr::pluralStudly

The `cstr::pluralStudly` method converts a singular word string formatted in studly caps case to its plural form. This function supports any of the languages support by CF's pluralizer:
```php
    $plural = cstr::pluralStudly('VerifiedHuman');

    // VerifiedHumans

    $plural = cstr::pluralStudly('UserFeedback');

    // UserFeedback
```
You may provide an integer as a second argument to the function to retrieve the singular or plural form of the string:
```php
    $plural = cstr::pluralStudly('VerifiedHuman', 2);

    // VerifiedHumans

    $singular = cstr::pluralStudly('VerifiedHuman', 1);

    // VerifiedHuman
```
### cstr::random

The `cstr::random` method generates a random string of the specified length. This function uses PHP's `random_bytes` function:
```php
    $random = cstr::random(40);
```
### cstr::remove

The `cstr::remove` method removes the given value or array of values from the string:
```php
    $string = 'Peter Piper picked a peck of pickled peppers.';

    $removed = cstr::remove('e', $string);

    // Ptr Pipr pickd a pck of pickld ppprs.
```
You may also pass `false` as a third argument to the `remove` method to ignore case when removing strings.

### cstr::replace

The `cstr::replace` method replaces a given string within the string:
```php
    $string = 'Cresenity 1.x';

    $replaced = cstr::replace('1.x', '2.x', $string);

    // Cresenity 2.x
```
### cstr::replaceArray

The `cstr::replaceArray` method replaces a given value in the string sequentially using an array:
```php
    $string = 'The event will take place between ? and ?';

    $replaced = cstr::replaceArray('?', ['8:30', '9:00'], $string);

    // The event will take place between 8:30 and 9:00
```
### cstr::replaceFirst

The `cstr::replaceFirst` method replaces the first occurrence of a given value in a string:
```php
    $replaced = cstr::replaceFirst('the', 'a', 'the quick brown fox jumps over the lazy dog');

    // a quick brown fox jumps over the lazy dog
```
### cstr::replaceLast

The `cstr::replaceLast` method replaces the last occurrence of a given value in a string:
```php
    $replaced = cstr::replaceLast('the', 'a', 'the quick brown fox jumps over the lazy dog');

    // the quick brown fox jumps over a lazy dog
```

### cstr::reverse()

The `cstr::reverse` method reverses the given string:
```php

    $reversed = cstr::reverse('Hello World');

    // dlroW olleH
```
### cstr::singular

The `cstr::singular` method converts a string to its singular form. This function supports any of the languages support by CF's pluralizer:
```php
    $singular = cstr::singular('cars');

    // car

    $singular = cstr::singular('children');

    // child
```
### cstr::slug

The `cstr::slug` method generates a URL friendly "slug" from the given string:
```php
    $slug = cstr::slug('Cresenity Framework', '-');

    // cresenity-framework
```
### cstr::snake

The `cstr::snake` method converts the given string to `snake_case`:
```php
    $converted = cstr::snake('fooBar');

    // foo_bar

    $converted = cstr::snake('fooBar', '-');

    // foo-bar
```
### cstr::squish

The `cstr::squish` method removes all extraneous white space from a string, including extraneous white space between words:
```php

    $string = cstr::squish('    cresenity    framework    ');

    // cresenity framework
```
### cstr::start

The `cstr::start` method adds a single instance of the given value to a string if it does not already start with that value:
```php
    $adjusted = cstr::start('this/string', '/');

    // /this/string

    $adjusted = cstr::start('/this/string', '/');

    // /this/string
```
### cstr::startsWith

The `cstr::startsWith` method determines if the given string begins with the given value:
```php

    $result = cstr::startsWith('This is my name', 'This');

    // true
```
If an array of possible values is passed, the `startsWith` method will return `true` if the string begins with any of the given values:
```php
    $result = cstr::startsWith('This is my name', ['This', 'That', 'There']);

    // true
```

### cstr::studly

The `cstr::studly` method converts the given string to `StudlyCase`:
```php
    $converted = cstr::studly('foo_bar');

    // FooBar
```
### cstr::substr

The `cstr::substr` method returns the portion of string specified by the start and length parameters:
```php
    $converted = cstr::substr('The Cresenity Framework', 4, 9);

    // Cresenity
```
### cstr::substrCount

The `cstr::substrCount` method returns the number of occurrences of a given value in the given string:
```php

    $count = cstr::substrCount('If you like ice cream, you will like snow cones.', 'like');

    // 2
```
### cstr::substrReplace

The `cstr::substrReplace` method replaces text within a portion of a string, starting at the position specified by the third argument and replacing the number of characters specified by the fourth argument. Passing `0` to the method's fourth argument will insert the string at the specified position without replacing any of the existing characters in the string:
```php
    $result = cstr::substrReplace('1300', ':', 2);
    // 13:

    $result = cstr::substrReplace('1300', ':', 2, 0);
    // 13:00
```
### cstr::swap

The `cstr::swap` method replaces multiple values in the given string using PHP's `strtr` function:
```php
    $string = cstr::swap([
        'Tacos' => 'Burritos',
        'great' => 'fantastic',
    ], 'Tacos are great!');

    // Burritos are fantastic!
```
### cstr::title()

The `cstr::title` method converts the given string to `Title Case`:
```php
    $converted = cstr::title('a nice title uses the correct case');

    // A Nice Title Uses The Correct Case
```
### cstr::toHtmlString()

The `cstr::toHtmlString` method converts the string instance to an instance of `CBase_HtmlString`, which may be displayed in Blade templates:
```php

    $htmlString = cstr::of('Some String')->toHtmlString();
```
### cstr::ucfirst

The `cstr::ucfirst` method returns the given string with the first character capitalized:
```php
    $string = cstr::ucfirst('foo bar');

    // Foo bar
```
### cstr::ucsplit

The `cstr::ucsplit` method splits the given string into an array by uppercase characters:
```php
    $segments = cstr::ucsplit('FooBar');

    // [0 => 'Foo', 1 => 'Bar']
```
### cstr::upper

The `cstr::upper` method converts the given string to uppercase:
```php
    $string = cstr::upper('cresenity');

    // CRESENITY
```
### cstr::ulid

The `cstr::ulid` method generates a ULID:
```php
    return (string) cstr::ulid();

    // 01gd6r360bp37zj17nxb55yv40
```
### cstr::uuid

The `cstr::uuid` method generates a UUID (version 4):
```php
    return (string) cstr::uuid();
```
### cstr::wordCount

The `cstr::wordCount` method returns the number of words that a string contains:

```php
cstr::wordCount('Hello, world!'); // 2
```
### cstr::words

The `cstr::words` method limits the number of words in a string. An additional string may be passed to this method via its third argument to specify which string should be appended to the end of the truncated string:
```php
    return cstr::words('Perfectly balanced, as all things should be.', 3, ' >>>');

    // Perfectly balanced, as >>>
```

### cstr::of


The `cstr::of` function returns a new `CBase_Stringable` instance of the given string:
```php
    $string = cstr::of('Cresenity')->append(' Framework');

    // 'Cresenity Framework'
```
