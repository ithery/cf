<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 9:00:18 PM
 */
final class CGeo_Query_GeocodeQuery implements CGeo_Interface_QueryInterface {
    /**
     * The address or text that should be geocoded.
     *
     * @var string
     */
    private $text;

    /**
     * @var CGeo_Model_Bounds|null
     */
    private $bounds;

    /**
     * @var string|null
     */
    private $locale;

    /**
     * @var int
     */
    private $limit = CGeo_Interface_GeocoderInterface::DEFAULT_RESULT_LIMIT;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @param string $text
     */
    private function __construct($text) {
        if (empty($text)) {
            throw new CGeo_Exception_InvalidArgument('Geocode query cannot be empty');
        }
        $this->text = $text;
    }

    /**
     * @param string $text
     *
     * @return CGeo_Query_GeocodeQuery
     */
    public static function create($text) {
        return new self($text);
    }

    /**
     * @param string $text
     *
     * @return CGeo_Query_GeocodeQuery
     */
    public function withText($text) {
        $new = clone $this;
        $new->text = $text;
        return $new;
    }

    /**
     * @param CGeo_Model_Bounds $bounds
     *
     * @return CGeo_Query_GeocodeQuery
     */
    public function withBounds(CGeo_Model_Bounds $bounds) {
        $new = clone $this;
        $new->bounds = $bounds;
        return $new;
    }

    /**
     * @param string $locale
     *
     * @return CGeo_Query_GeocodeQuery
     */
    public function withLocale($locale) {
        $new = clone $this;
        $new->locale = $locale;
        return $new;
    }

    /**
     * @param int $limit
     *
     * @return CGeo_Query_GeocodeQuery
     */
    public function withLimit($limit) {
        $new = clone $this;
        $new->limit = $limit;
        return $new;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return CGeo_Query_GeocodeQuery
     */
    public function withData($name, $value) {
        $new = clone $this;
        $new->data[$name] = $value;
        return $new;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @return Bounds|null
     */
    public function getBounds() {
        return $this->bounds;
    }

    /**
     * @return string|null
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * @return int
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getData($name, $default = null) {
        if (!array_key_exists($name, $this->data)) {
            return $default;
        }
        return $this->data[$name];
    }

    /**
     * @return array
     */
    public function getAllData() {
        return $this->data;
    }

    /**
     * String for logging. This is also a unique key for the query
     *
     * @return string
     */
    public function __toString() {
        return sprintf('GeocodeQuery: %s', json_encode([
            'text' => $this->getText(),
            'bounds' => $this->getBounds() ? $this->getBounds()->toArray() : 'null',
            'locale' => $this->getLocale(),
            'limit' => $this->getLimit(),
            'data' => $this->getAllData(),
        ]));
    }
}
