<?php

defined('SYSPATH') or die('No direct access allowed.');
use Opis\Closure\SerializableClosure;

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 12, 2018, 9:08:07 PM
 */
class CElement_Component_ActionRow extends CElement_Component_Action {
    protected $rowCallback;

    public function __construct($id = null) {
        parent::__construct($id);
        $this->rowCallback = null;
    }

    public static function factory($id = null) {
        return new static($id);
    }

    public function withRowCallback($callback) {
        $this->rowCallback = new SerializableClosure($callback);
    }

    public function applyRowCallback($row) {
        if ($this->rowCallback && $this->rowCallback instanceof SerializableClosure) {
            $this->rowCallback->__invoke($this, $row);
        }

        return $this;
    }
}