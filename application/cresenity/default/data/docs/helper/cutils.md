# Helper cutils

The `cutils` helper class provides date arithmetic, time differences, Indonesian number
formatting, and string sanitisation.

## Time differences

Each of the difference methods takes a required `$from` and an optional `$to` that defaults
to the current time. Both accept a date string or a timestamp.

```php
cutils::year_diff('2020-01-01');
cutils::month_diff('2026-01-01');
cutils::day_diff('2026-08-01', '2026-08-04');            // 3
cutils::hour_diff('2026-08-04 01:00', '2026-08-04 09:00'); // 8
cutils::minute_diff($from, $to);
cutils::second_diff($from, $to);
```

### cutils::human_time_diff

The `cutils::human_time_diff` method returns the difference expressed in the largest
appropriate unit:

```php
$diff = cutils::human_time_diff('2026-08-04 09:00', '2026-08-04 09:45');

// 45 minutes ago

$diff = cutils::human_time_diff('2026-06-01');

// 2 months ago
```

The suffix may be replaced, and the number may be spelled out:

```php
cutils::human_time_diff($from, '', false, ' yang lalu');

// 2 months yang lalu

cutils::human_time_diff($from, '', true);

// two months ago
```

The unit escalates automatically: past seven days the result is expressed in weeks, past a
month in months, and so on.

## Dates

```php
cutils::get_day('2026-08-04');     // 04
cutils::get_month('2026-08-04');   // 08
cutils::get_year('2026-08-04');    // 2026

cutils::begin_date_month();        // first day of the current month
cutils::last_date_month();         // last day of the current month
cutils::day_count(8, 2026);        // number of days in the given month
```

### List helpers

These return arrays suitable for select inputs:

```php
cutils::day_list();          // 1..31
cutils::day_name_list();     // day names
cutils::month_list();        // 1 => Januari, ... (translated)
cutils::month_list(false);   // 1 => January, ... (untranslated)
cutils::year_list(2020, 2030);
cutils::month_name(8);       // Agustus
cutils::month_romawi(8);     // VIII
```

`cutils::month_romawi` returns the month as a Roman numeral, used in Indonesian document
numbering such as `042/INV/VIII/2026`.

## Numbers

### cutils::indonesian_currency_string

The `cutils::indonesian_currency_string` method spells a number out in Indonesian, for use on
invoices and receipts:

```php
$words = cutils::indonesian_currency_string(1250000);

// Satu Juta Dua Ratus Lima Puluh Ribu
```

### cutils::number_to_word

The `cutils::number_to_word` method spells a number out in English:

```php
$words = cutils::number_to_word(456);

// four hundred and fifty-six
```

### cutils::thousand_separator

The `cutils::thousand_separator` method formats a number using a dot as the thousands
separator, following Indonesian convention:

```php
cutils::thousand_separator(1234567);

// 1.234.567
```

For locale-aware formatting, use [`cnum::format`](/docs/helper/cnum).

### cutils::format_filesize

The `cutils::format_filesize` method converts a byte count into a file size using lowercase
units:

```php
cutils::format_filesize(1048576);

// 1 mb
```

[`cnum::fileSize`](/docs/helper/cnum) produces uppercase units.

## Strings

### cutils::sanitize

The `cutils::sanitize` method converts a string into a lowercase, dash-separated form
suitable for URLs:

```php
cutils::sanitize('Laporan Bulan Agustus 2026!');

// laporan-bulan-agustus-2026
```

Passing `true` as the second argument preserves dots, underscores, and tildes, which keeps
file extensions intact:

```php
cutils::sanitize('Laporan v1.2.pdf', true);

// laporan-v1.2.pdf
```

### cutils::sanitize_msisdn

The `cutils::sanitize_msisdn` method normalises a phone number to a country-code form without
a leading plus sign:

```php
cutils::sanitize_msisdn('08123456789');         // 628123456789
cutils::sanitize_msisdn('+628123456789');       // 628123456789
cutils::sanitize_msisdn('08123456789', '65');   // 658123456789
```

Only the prefix is normalised. Spaces and dashes are preserved, so numbers coming from user
input or CSV imports should be stripped of non-digits first.

### cutils::randmd5

The `cutils::randmd5` method returns the MD5 hash of a random integer:

```php
cutils::randmd5();

// 9f2c1e7d...
```

> The underlying value is `rand(0, 9999)`, giving ten thousand possible outputs, all of which
> can be precomputed. Use it for temporary filename suffixes only. For tokens, one-time
> codes, or any value that protects something, use `cstr::random()` or `random_bytes()`.

### cutils::trim_csv

The `cutils::trim_csv` method prepares text for writing into a single CSV cell:

```php
$value = cutils::trim_csv($text);
```

## Output

```php
cutils::br();            // <br />
cutils::indent(2);       // two tab characters
cutils::indent(4, ' ');  // four spaces
```
