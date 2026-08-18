<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @method CElement_Component_ListGroup setDataFromCallback($callback, $callbackOptions = [], $require = null)
 */
class CElement_Component_Kanban_List extends CElement_Component_Widget {
    /**
     * @var CElement_Component_ListGroup
     */
    protected $kanbanBox;

    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id) {
        parent::__construct($id);
        $this->setNoPadding();
        $this->kanbanBox = $this->content->addListGroup()->addClass('kanban-box px-2 pt-2');
        $this->wrapper = $this->kanbanBox;
    }

    /**
     * @return void
     */
    public function build() {
        parent::build();
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        if (is_callable([$this->kanbanBox, $method])) {
            return call_user_func_array([$this->kanbanBox, $method], $parameters);
        } else {
            throw new Exception('not callable method:' . $method);
        }
        parent::__call($method, $parameters);
    }
}
