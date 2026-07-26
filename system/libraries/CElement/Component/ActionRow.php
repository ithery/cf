<?php

defined('SYSPATH') or die('No direct access allowed.');
use Opis\Closure\SerializableClosure;

class CElement_Component_ActionRow extends CElement_Component_Action {
    /**
     * @var null|SerializableClosure
     */
    protected $rowCallback;

    /**
     * @param null|string $id
     *
     * @return void
     */
    public function __construct($id = null) {
        parent::__construct($id);
        $this->rowCallback = null;
    }

    /**
     * @param null|string $id
     *
     * @return static
     */
    public static function factory($id = null) {
        // @phpstan-ignore-next-line
        return new static($id);
    }

    /**
     * @param Closure $callback
     *
     * @return $this
     */
    public function withRowCallback($callback) {
        $this->rowCallback = new SerializableClosure($callback);

        return $this;
    }

    /**
     * @param mixed $row
     *
     * @return $this
     */
    public function applyRowCallback($row) {
        if ($this->rowCallback && $this->rowCallback instanceof SerializableClosure) {
            $this->rowCallback->__invoke($this, $row);
        }

        return $this;
    }
}
