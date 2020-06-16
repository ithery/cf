<?php

//declare(strict_types = 1);

namespace Embed;

use Psr\Http\Message\UriInterface;

require_once dirname(__FILE__) . '/functions.php';


trait ApiTrait {

    private $extractor;
    private $data;

    public function __construct(Extractor $extractor) {
        $this->extractor = $extractor;
    }

    public function all() {
        if (!isset($this->data)) {
            $this->data = $this->fetchData();
        }

        return $this->data;
    }

    public function get(...$keys) {
        $data = $this->all();

        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                return null;
            }

            $data = $data[$key];
        }

        return $data;
    }

    public function str(...$keys) {
        $value = $this->get(...$keys);

        if (is_array($value)) {
            $value = array_shift($value);
        }

        return $value ? clean((string) $value) : null;
    }

    public function strAll(...$keys) {
        $all = (array) $this->get(...$keys);
        return array_filter(array_map(function ($value) {
                    return clean($value);
                }, $all));
    }

    public function html(...$keys) {
        $value = $this->get(...$keys);

        if (is_array($value)) {
            $value = array_shift($value);
        }

        return $value ? clean((string) $value, true) : null;
    }

    public function int(...$keys) {
        $value = $this->get(...$keys);

        if (is_array($value)) {
            $value = array_shift($value);
        }

        return $value ? (int) $value : null;
    }

    public function url(...$keys) {
        $url = $this->str(...$keys);

        return $url ? $this->extractor->resolveUri($url) : null;
    }

    public function time(...$keys) {
        $time = $this->str(...$keys);
        $datetime = $time ? date_create($time) : null;

        if (!$datetime && ctype_digit($time)) {
            $datetime = date_create_from_format('U', $time);
        }

        return ($datetime && $datetime->getTimestamp() > 0) ? $datetime : null;
    }

    abstract protected function fetchData();
}
