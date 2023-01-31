<?php
/**
 * Class CMetric_Metric.
 *
 * @see CMetric
 */
class CMetric_Metric {
    use CMetric_Trait_HasDriverTrait;

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $value;

    /**
     * @var string
     */
    protected $unit;

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * @var array
     */
    protected $extra = [];

    /**
     * @var
     */
    protected $timestamp;

    /**
     * @var int
     */
    protected $resolution;

    /**
     * Metric constructor.
     *
     * @param $name
     * @param $value
     * @param $driver
     */
    public function __construct($name = null, $value = null, $driver = null) {
        $this->setName($name);
        $this->setValue($value);

        $this->driver = $driver;
    }

    /**
     * @return int
     */
    public function getResolution() {
        return $this->resolution;
    }

    /**
     * @param int $resolution
     *
     * @return $this
     */
    public function setResolution($resolution) {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnit() {
        return $this->unit;
    }

    /**
     * @param string $unit
     *
     * @return $this
     */
    public function setUnit($unit) {
        $this->unit = $unit;

        return $this;
    }

    /**
     * @return array
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * @param array $tags
     *
     * @return $this
     */
    public function setTags(array $tags) {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addTag($key, $value) {
        $this->tags[$key] = $this->escapeFieldValue($value);
    }

    /**
     * @return array
     */
    public function getExtra() {
        return $this->extra;
    }

    /**
     * @param array $extra
     *
     * @return $this
     */
    public function setExtra(array $extra) {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addExtra($key, $value) {
        if (is_array($value)) {
            return $this->addExtraArray($key, $value);
        }

        $this->extra[$key] = $this->escapeFieldValue($value);

        return $this;
    }

    private function escapeFieldValue($value) {
        $value = str_replace([' ', '"' . '\\'], ['\ ', '\"', '\\\\'], $value);

        return $value;
    }

    /**
     * @param string $key
     * @param array  $value
     * @param string $separator
     *
     * @return $this
     */
    public function addExtraArray($key, array $value, $separator = '_') {
        $array = $this->flattenArray($value, $separator, $key . $separator);
        foreach ($array as $fieldKey => $fieldVal) {
            $this->addExtra($fieldKey, $fieldVal);
        }

        return $this;
    }

    /**
     * @param array  $value
     * @param string $separator
     * @param string $prepend
     *
     * @return array
     */
    private function flattenArray(array $array, $separator = '_', $prepend = '') {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, $this->flattenArray($value, $separator, $prepend . $key . $separator));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    /**
     * @return mixed
     */
    public function getTimestamp() {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     *
     * @return $this
     */
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return mixed
     */
    public function add() {
        $this->getDriver()->add($this);
    }

    /**
     * @return mixed
     */
    public function format() {
        return $this->getDriver()->format($this);
    }
}
