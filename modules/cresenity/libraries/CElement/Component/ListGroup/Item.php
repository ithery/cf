<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 2, 2019, 11:14:24 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_ListGroup_Item extends CElement_Element {

    /**
     *  Callback for this item, when not defined will use default renderer
     * 
     * @var callable
     */
    protected $callback;

    /**
     * Data for this item
     * 
     * @var array
     */
    protected $data;
    
    /**
     * Index for this item
     * 
     * @var int
     */
    protected $index;

    /**
     *  path of file to need to be require manually
     * 
     * @var string 
     */
    protected $callbackRequire;

    public function __construct($id) {
        parent::__construct($id);
    }

    public function setCallback($callback, $require = "") {
        $this->callback = CHelper::closure()->serializeClosure($callback);
        if (strlen($require) > 0) {
            $this->callbackRequire = $require;
        }
        return $this;
    }

    public function setIndex($index) {
        $this->index = $index;
        return $this;
    }
    
    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    public function build() {
        $this->addClass('list-group-item');
        $js = '';
        if ($this->callback != null) {
            $htmlValue = CFunction::factory($this->callback)
                    ->addArg($this)
                    ->addArg($this->data)
                    ->setRequire($this->callbackRequire)
                    ->execute();
            if (is_array($htmlValue) && isset($htmlValue['html']) && isset($htmlValue['js'])) {
                $js .= $htmlValue['js'];
                $htmlValue = $htmlValue['html'];
            }
            $this->addJs($js);
            $this->add($htmlValue);
        } else {
            $title = carr::get($this->data, 'title');
            $content = carr::get($this->data, 'content');
            $this->addH5()->add($title);
            $this->addDiv()->add($content);
        }
    }

}
