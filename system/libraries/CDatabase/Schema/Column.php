<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 11:41:09 AM
 */

/**
 * Object representation of a database column.
 */
class CDatabase_Schema_Column extends CDatabase_AbstractAsset {
    /**
     * @var CDatabase_Type
     */
    protected $type;

    /**
     * @var int|null
     */
    protected $length = null;

    /**
     * @var int
     */
    protected $precision = 10;

    /**
     * @var int
     */
    protected $scale = 0;

    /**
     * @var bool
     */
    protected $unsigned = false;

    /**
     * @var bool
     */
    protected $fixed = false;

    /**
     * @var bool
     */
    protected $notnull = true;

    /**
     * @var string|null
     */
    protected $default = null;

    /**
     * @var bool
     */
    protected $autoincrement = false;

    /**
     * @var array
     */
    protected $platformOptions = [];

    /**
     * @var string|null
     */
    protected $columnDefinition = null;

    /**
     * @var string|null
     */
    protected $comment = null;

    /**
     * @var array
     */
    protected $customSchemaOptions = [];

    /**
     * Creates a new Column.
     *
     * @param string         $columnName
     * @param CDatabase_Type $type
     * @param array          $options
     */
    public function __construct($columnName, CDatabase_Type $type, array $options = []) {
        $this->setName($columnName);
        $this->setType($type);
        $this->setOptions($options);
    }

    /**
     * @param array $options
     *
     * @return Column
     */
    public function setOptions(array $options) {
        foreach ($options as $name => $value) {
            $method = 'set' . $name;
            if (!method_exists($this, $method)) {
                // next major: throw an exception
                @trigger_error(sprintf(
                    'The "%s" column option is not supported,'
                                        . ' setting it is deprecated and will cause an error in Doctrine 3.0',
                    $name
                ), E_USER_DEPRECATED);

                continue;
            }
            $this->$method($value);
        }

        return $this;
    }

    /**
     * @param CDatabase_Type $type
     *
     * @return CDatabase_Schema_Column
     */
    public function setType(CDatabase_Type $type) {
        $this->type = $type;

        return $this;
    }

    /**
     * @param int|null $length
     *
     * @return CDatabase_Schema_Column
     */
    public function setLength($length) {
        if ($length !== null) {
            $this->length = (int) $length;
        } else {
            $this->length = null;
        }

        return $this;
    }

    /**
     * @param int $precision
     *
     * @return CDatabase_Schema_Column
     */
    public function setPrecision($precision) {
        if (!is_numeric($precision)) {
            $precision = 10; // defaults to 10 when no valid precision is given.
        }

        $this->precision = (int) $precision;

        return $this;
    }

    /**
     * @param int $scale
     *
     * @return CDatabase_Schema_Column
     */
    public function setScale($scale) {
        if (!is_numeric($scale)) {
            $scale = 0;
        }

        $this->scale = (int) $scale;

        return $this;
    }

    /**
     * @param bool $unsigned
     *
     * @return CDatabase_Schema_Column
     */
    public function setUnsigned($unsigned) {
        $this->unsigned = (bool) $unsigned;

        return $this;
    }

    /**
     * @param bool $fixed
     *
     * @return CDatabase_Schema_Column
     */
    public function setFixed($fixed) {
        $this->fixed = (bool) $fixed;

        return $this;
    }

    /**
     * @param bool $notnull
     *
     * @return CDatabase_Schema_Column
     */
    public function setNotnull($notnull) {
        $this->notnull = (bool) $notnull;

        return $this;
    }

    /**
     * @param mixed $default
     *
     * @return CDatabase_Schema_Column
     */
    public function setDefault($default) {
        $this->default = $default;

        return $this;
    }

    /**
     * @param array $platformOptions
     *
     * @return CDatabase_Schema_Column
     */
    public function setPlatformOptions(array $platformOptions) {
        $this->platformOptions = $platformOptions;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return CDatabase_Schema_Column
     */
    public function setPlatformOption($name, $value) {
        $this->platformOptions[$name] = $value;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return CDatabase_Schema_Column
     */
    public function setColumnDefinition($value) {
        $this->columnDefinition = $value;

        return $this;
    }

    /**
     * @return CDatabase_Type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return int|null
     */
    public function getLength() {
        return $this->length;
    }

    /**
     * @return int
     */
    public function getPrecision() {
        return $this->precision;
    }

    /**
     * @return int
     */
    public function getScale() {
        return $this->scale;
    }

    /**
     * @return bool
     */
    public function getUnsigned() {
        return $this->unsigned;
    }

    /**
     * @return bool
     */
    public function getFixed() {
        return $this->fixed;
    }

    /**
     * @return bool
     */
    public function getNotnull() {
        return $this->notnull;
    }

    /**
     * @return string|null
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * @return array
     */
    public function getPlatformOptions() {
        return $this->platformOptions;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasPlatformOption($name) {
        return isset($this->platformOptions[$name]);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getPlatformOption($name) {
        return $this->platformOptions[$name];
    }

    /**
     * @return string|null
     */
    public function getColumnDefinition() {
        return $this->columnDefinition;
    }

    /**
     * @return bool
     */
    public function getAutoincrement() {
        return $this->autoincrement;
    }

    /**
     * @param bool $flag
     *
     * @return CDatabase_Schema_Column
     */
    public function setAutoincrement($flag) {
        $this->autoincrement = $flag;

        return $this;
    }

    /**
     * @param string $comment
     *
     * @return CDatabase_Schema_Column
     */
    public function setComment($comment) {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return CDatabase_Schema_Column
     */
    public function setCustomSchemaOption($name, $value) {
        $this->customSchemaOptions[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasCustomSchemaOption($name) {
        return isset($this->customSchemaOptions[$name]);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getCustomSchemaOption($name) {
        return $this->customSchemaOptions[$name];
    }

    /**
     * @param array $customSchemaOptions
     *
     * @return CDatabase_Schema_Column
     */
    public function setCustomSchemaOptions(array $customSchemaOptions) {
        $this->customSchemaOptions = $customSchemaOptions;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomSchemaOptions() {
        return $this->customSchemaOptions;
    }

    /**
     * @return array
     */
    public function toArray() {
        return array_merge([
            'name' => $this->name,
            'type' => $this->type,
            'default' => $this->default,
            'notnull' => $this->notnull,
            'length' => $this->length,
            'precision' => $this->precision,
            'scale' => $this->scale,
            'fixed' => $this->fixed,
            'unsigned' => $this->unsigned,
            'autoincrement' => $this->autoincrement,
            'columnDefinition' => $this->columnDefinition,
            'comment' => $this->comment,
        ], $this->platformOptions, $this->customSchemaOptions);
    }
}
