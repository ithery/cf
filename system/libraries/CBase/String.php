<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
use Symfony\Component\VarDumper\VarDumper;

class CBase_String implements Stringable {
    use CTrait_Macroable;

    /**
     * The underlying string value.
     *
     * @var string
     */
    protected $value;

    /**
     * Create a new instance of the class.
     *
     * @param string $value
     *
     * @return void
     */
    public function __construct($value = '') {
        $this->value = (string) $value;
    }

    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * @param string $search
     *
     * @return static
     */
    public function after($search) {
        return new static(cstr::after($this->value, $search));
    }

    /**
     * Return the remainder of a string after the last occurrence of a given value.
     *
     * @param string $search
     *
     * @return static
     */
    public function afterLast($search) {
        return new static(cstr::afterLast($this->value, $search));
    }

    /**
     * Append the given values to the string.
     *
     * @param array $values
     *
     * @return static
     */
    public function append(...$values) {
        return new static($this->value . implode('', $values));
    }

    /**
     * Transliterate a UTF-8 value to ASCII.
     *
     * @param string $language
     *
     * @return static
     */
    public function ascii($language = 'en') {
        return new static(cstr::ascii($this->value, $language));
    }

    /**
     * Get the trailing name component of the path.
     *
     * @param string $suffix
     *
     * @return static
     */
    public function basename($suffix = '') {
        return new static(basename($this->value, $suffix));
    }

    /**
     * Get the basename of the class path.
     *
     * @return static
     */
    public function classBasename() {
        return new static(c::classBasename($this->value));
    }

    /**
     * Get the portion of a string before the first occurrence of a given value.
     *
     * @param string $search
     *
     * @return static
     */
    public function before($search) {
        return new static(cstr::before($this->value, $search));
    }

    /**
     * Get the portion of a string before the last occurrence of a given value.
     *
     * @param string $search
     *
     * @return static
     */
    public function beforeLast($search) {
        return new static(cstr::beforeLast($this->value, $search));
    }

    /**
     * Get the portion of a string between two given values.
     *
     * @param string $from
     * @param string $to
     *
     * @return static
     */
    public function between($from, $to) {
        return new static(cstr::between($this->value, $from, $to));
    }

    /**
     * Convert a value to camel case.
     *
     * @return static
     */
    public function camel() {
        return new static(cstr::camel($this->value));
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param string|array $needles
     *
     * @return bool
     */
    public function contains($needles) {
        return cstr::contains($this->value, $needles);
    }

    /**
     * Determine if a given string contains all array values.
     *
     * @param array $needles
     *
     * @return bool
     */
    public function containsAll(array $needles) {
        return cstr::containsAll($this->value, $needles);
    }

    /**
     * Get the parent directory's path.
     *
     * @param int $levels
     *
     * @return static
     */
    public function dirname($levels = 1) {
        return new static(dirname($this->value, $levels));
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string|array $needles
     *
     * @return bool
     */
    public function endsWith($needles) {
        return cstr::endsWith($this->value, $needles);
    }

    /**
     * Determine if the string is an exact match with the given value.
     *
     * @param string $value
     *
     * @return bool
     */
    public function exactly($value) {
        return $this->value === $value;
    }

    /**
     * Explode the string into an array.
     *
     * @param string $delimiter
     * @param int    $limit
     *
     * @return CCollection
     */
    public function explode($delimiter, $limit = PHP_INT_MAX) {
        return c::collect(explode($delimiter, $this->value, $limit));
    }

    /**
     * Split a string using a regular expression or by length.
     *
     * @param string|int $pattern
     * @param int        $limit
     * @param int        $flags
     *
     * @return CCollection
     */
    public function split($pattern, $limit = -1, $flags = 0) {
        if (filter_var($pattern, FILTER_VALIDATE_INT) !== false) {
            return c::collect(mb_str_split($this->value, $pattern));
        }

        $segments = preg_split($pattern, $this->value, $limit, $flags);

        return !empty($segments) ? c::collect($segments) : c::collect();
    }

    /**
     * Cap a string with a single instance of a given value.
     *
     * @param string $cap
     *
     * @return static
     */
    public function finish($cap) {
        return new static(cstr::finish($this->value, $cap));
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param string|array $pattern
     *
     * @return bool
     */
    public function is($pattern) {
        return cstr::is($pattern, $this->value);
    }

    /**
     * Determine if a given string is 7 bit ASCII.
     *
     * @return bool
     */
    public function isAscii() {
        return cstr::isAscii($this->value);
    }

    /**
     * Determine if the given string is empty.
     *
     * @return bool
     */
    public function isEmpty() {
        return $this->value === '';
    }

    /**
     * Determine if the given string is not empty.
     *
     * @return bool
     */
    public function isNotEmpty() {
        return !$this->isEmpty();
    }

    /**
     * Convert a string to kebab case.
     *
     * @return static
     */
    public function kebab() {
        return new static(cstr::kebab($this->value));
    }

    /**
     * Return the length of the given string.
     *
     * @param string $encoding
     *
     * @return int
     */
    public function length($encoding = null) {
        return cstr::length($this->value, $encoding);
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param int    $limit
     * @param string $end
     *
     * @return static
     */
    public function limit($limit = 100, $end = '...') {
        return new static(cstr::limit($this->value, $limit, $end));
    }

    /**
     * Convert the given string to lower-case.
     *
     * @return static
     */
    public function lower() {
        return new static(cstr::lower($this->value));
    }

    /**
     * Get the string matching the given pattern.
     *
     * @param string $pattern
     *
     * @return null|static
     */
    public function match($pattern) {
        preg_match($pattern, $this->value, $matches);

        if (!$matches) {
            return new static();
        }

        return new static(isset($matches[1]) ? $matches[1] : $matches[0]);
    }

    /**
     * Get the string matching the given pattern.
     *
     * @param string $pattern
     *
     * @return CCollection
     */
    public function matchAll($pattern) {
        preg_match_all($pattern, $this->value, $matches);

        if (empty($matches[0])) {
            return c::collect();
        }

        return c::collect(isset($matches[1]) ? $matches[1] : $matches[0]);
    }

    /**
     * Pad both sides of the string with another.
     *
     * @param int    $length
     * @param string $pad
     *
     * @return static
     */
    public function padBoth($length, $pad = ' ') {
        return new static(cstr::padBoth($this->value, $length, $pad));
    }

    /**
     * Pad the left side of the string with another.
     *
     * @param int    $length
     * @param string $pad
     *
     * @return static
     */
    public function padLeft($length, $pad = ' ') {
        return new static(cstr::padLeft($this->value, $length, $pad));
    }

    /**
     * Pad the right side of the string with another.
     *
     * @param int    $length
     * @param string $pad
     *
     * @return static
     */
    public function padRight($length, $pad = ' ') {
        return new static(cstr::padRight($this->value, $length, $pad));
    }

    /**
     * Parse a Class@method style callback into class and method.
     *
     * @param null|string $default
     *
     * @return array
     */
    public function parseCallback($default = null) {
        return cstr::parseCallback($this->value, $default);
    }

    /**
     * Get the plural form of an English word.
     *
     * @param int $count
     *
     * @return static
     */
    public function plural($count = 2) {
        return new static(cstr::plural($this->value, $count));
    }

    /**
     * Pluralize the last word of an English, studly caps case string.
     *
     * @param int $count
     *
     * @return static
     */
    public function pluralStudly($count = 2) {
        return new static(cstr::pluralStudly($this->value, $count));
    }

    /**
     * Prepend the given values to the string.
     *
     * @param array $values
     *
     * @return static
     */
    public function prepend(...$values) {
        return new static(implode('', $values) . $this->value);
    }

    /**
     * Replace the given value in the given string.
     *
     * @param string|string[] $search
     * @param string|string[] $replace
     *
     * @return static
     */
    public function replace($search, $replace) {
        return new static(str_replace($search, $replace, $this->value));
    }

    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param string $search
     * @param array  $replace
     *
     * @return static
     */
    public function replaceArray($search, array $replace) {
        return new static(cstr::replaceArray($search, $replace, $this->value));
    }

    /**
     * Replace the first occurrence of a given value in the string.
     *
     * @param string $search
     * @param string $replace
     *
     * @return static
     */
    public function replaceFirst($search, $replace) {
        return new static(cstr::replaceFirst($search, $replace, $this->value));
    }

    /**
     * Replace the last occurrence of a given value in the string.
     *
     * @param string $search
     * @param string $replace
     *
     * @return static
     */
    public function replaceLast($search, $replace) {
        return new static(cstr::replaceLast($search, $replace, $this->value));
    }

    /**
     * Replace the patterns matching the given regular expression.
     *
     * @param string          $pattern
     * @param \Closure|string $replace
     * @param int             $limit
     *
     * @return static
     */
    public function replaceMatches($pattern, $replace, $limit = -1) {
        if ($replace instanceof Closure) {
            return new static(preg_replace_callback($pattern, $replace, $this->value, $limit));
        }

        return new static(preg_replace($pattern, $replace, $this->value, $limit));
    }

    /**
     * Begin a string with a single instance of a given value.
     *
     * @param string $prefix
     *
     * @return static
     */
    public function start($prefix) {
        return new static(cstr::start($this->value, $prefix));
    }

    /**
     * Convert the given string to upper-case.
     *
     * @return static
     */
    public function upper() {
        return new static(cstr::upper($this->value));
    }

    /**
     * Convert the given string to title case.
     *
     * @return static
     */
    public function title() {
        return new static(cstr::title($this->value));
    }

    /**
     * Get the singular form of an English word.
     *
     * @return static
     */
    public function singular() {
        return new static(cstr::singular($this->value));
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param string      $separator
     * @param null|string $language
     *
     * @return static
     */
    public function slug($separator = '-', $language = 'en') {
        return new static(cstr::slug($this->value, $separator, $language));
    }

    /**
     * Convert a string to snake case.
     *
     * @param string $delimiter
     *
     * @return static
     */
    public function snake($delimiter = '_') {
        return new static(cstr::snake($this->value, $delimiter));
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string|array $needles
     *
     * @return bool
     */
    public function startsWith($needles) {
        return cstr::startsWith($this->value, $needles);
    }

    /**
     * Convert a value to studly caps case.
     *
     * @return static
     */
    public function studly() {
        return new static(cstr::studly($this->value));
    }

    /**
     * Returns the portion of string specified by the start and length parameters.
     *
     * @param int      $start
     * @param null|int $length
     *
     * @return static
     */
    public function substr($start, $length = null) {
        return new static(cstr::substr($this->value, $start, $length));
    }

    /**
     * Returns the number of substring occurrences.
     *
     * @param string   $needle
     * @param null|int $offset
     * @param null|int $length
     *
     * @return int
     */
    public function substrCount($needle, $offset = null, $length = null) {
        return cstr::substrCount($this->value, $needle, $offset, $length);
    }

    /**
     * Trim the string of the given characters.
     *
     * @param string $characters
     *
     * @return static
     */
    public function trim($characters = null) {
        return new static(trim(...array_merge([$this->value], func_get_args())));
    }

    /**
     * Left trim the string of the given characters.
     *
     * @param string $characters
     *
     * @return static
     */
    public function ltrim($characters = null) {
        return new static(ltrim(...array_merge([$this->value], func_get_args())));
    }

    /**
     * Right trim the string of the given characters.
     *
     * @param string $characters
     *
     * @return static
     */
    public function rtrim($characters = null) {
        return new static(rtrim(...array_merge([$this->value], func_get_args())));
    }

    /**
     * Make a string's first character uppercase.
     *
     * @return static
     */
    public function ucfirst() {
        return new static(cstr::ucfirst($this->value));
    }

    /**
     * Apply the callback's string changes if the given "value" is true.
     *
     * @param mixed         $value
     * @param callable      $callback
     * @param null|callable $default
     *
     * @return mixed|$this
     */
    public function when($value, $callback, $default = null) {
        if ($value) {
            return $callback($this, $value) ?: $this;
        } elseif ($default) {
            return $default($this, $value) ?: $this;
        }

        return $this;
    }

    /**
     * Execute the given callback if the string is empty.
     *
     * @param callable $callback
     *
     * @return static
     */
    public function whenEmpty($callback) {
        if ($this->isEmpty()) {
            $result = $callback($this);

            return is_null($result) ? $this : $result;
        }

        return $this;
    }

    /**
     * Limit the number of words in a string.
     *
     * @param int    $words
     * @param string $end
     *
     * @return static
     */
    public function words($words = 100, $end = '...') {
        return new static(cstr::words($this->value, $words, $end));
    }

    /**
     * Dump the string.
     *
     * @return $this
     */
    public function dump() {
        VarDumper::dump($this->value);

        return $this;
    }

    /**
     * Dump the string and end the script.
     *
     * @return void
     */
    public function dd() {
        $this->dump();

        exit(1);
    }

    /**
     * Proxy dynamic properties onto methods.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key) {
        return $this->{$key}();
    }

    /**
     * Get the raw string value.
     *
     * @return string
     */
    public function __toString() {
        return (string) $this->value;
    }
}
