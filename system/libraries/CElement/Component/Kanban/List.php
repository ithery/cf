<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 3, 2019, 1:46:52 AM
 */

/**
 * @method string setDataFromCallback($callback,$require)
 */
class CElement_Component_Kanban_List extends CElement_Component_Widget {
    protected $kanbanBox;

    public function __construct($id) {
        parent::__construct($id);
        $this->setNoPadding();
        $this->kanbanBox = $this->content->addListGroup()->addClass('kanban-box px-2 pt-2');
        $this->wrapper = $this->kanbanBox;
    }

    public function build() {
        parent::build();
    }

    public function __call($method, $parameters) {
        if (is_callable([$this->kanbanBox, $method])) {
            return call_user_func_array([$this->kanbanBox, $method], $parameters);
        } else {
            throw new Exception('not callable method:' . $method);
        }
        parent::__call($method, $parameters);
    }
}
