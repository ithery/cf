import echo from 'locutus/php/strings/echo';
import ucfirst from 'locutus/php/strings/ucfirst';
import strpos from 'locutus/php/strings/strpos';
import strlen from 'locutus/php/strings/strlen';
import strtotime from 'locutus/php/datetime/strtotime';
import is_numeric from 'locutus/php/var/is_numeric';
import array_diff from 'locutus/php/array/array_diff';
import str_replace from 'locutus/php/strings/str_replace';
import number_format from 'locutus/php/strings/number_format';
import date from 'locutus/php/datetime/date';
import preg_replace from 'locutus/php/pcre/preg_replace';
import serialize from 'locutus/php/var/serialize';
import unserialize from 'locutus/php/var/unserialize';

export default {
    echo,
    strtotime,
    strpos,
    strlen,
    is_numeric,
    array_diff,
    ucfirst,
    str_replace,
    number_format,
    date,
    preg_replace,
    serialize,
    unserialize
};
