<?php

class CElement_Component_Alert extends CElement_Component {
    use CTrait_Element_Property_Title;

    /**
     * @var CElement_Element_H4
     */
    protected $header;

    protected $content;

    protected $type;

    protected $isDismissable;

    /**
     * @var CElement_Element_Button
     */
    protected $dismissableButton;

    public function __construct($id = '', $tag = 'div') {
        parent::__construct($id, $tag);
        $this->dismissableButton = $this->addButton()->addClass('btn-close close')->setAttr([
            'data-dismiss' => 'alert',
            'data-bs-dismiss' => 'alert',
            'aria-hidden' => 'true',
        ])->add('×');
        $this->header = $this->addH4();
        $this->content = $this->addDiv()->addClass(' clearfix');
        $this->addClass('alert');
        $this->wrapper = $this->content;
        $this->tag = 'div';
        $this->isDismissable = false;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeDanger() {
        return $this->setType('error');
    }

    /**
     * @return $this
     */
    public function setTypeError() {
        return $this->setType('error');
    }

    /**
     * @return $this
     */
    public function setTypeSuccess() {
        return $this->setType('success');
    }

    /**
     * @return $this
     */
    public function setTypeWarning() {
        return $this->setType('warning');
    }

    /**
     * @return $this
     */
    public function setTypeInfo() {
        return $this->setType('info');
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setDismissable($bool = true) {
        $this->isDismissable = $bool;

        return $this;
    }

    public function build() {
        if (strlen($this->title) == 0) {
            $this->header->setVisibility(false);
        }
        if (!$this->isDismissable) {
            $this->dismissableButton->setVisibility(false);
        } else {
            $this->addClass('alert-dismissible');
        }
        $this->header->add($this->getTranslationTitle());
        switch ($this->type) {
            case 'error':
                $this->addClass('alert-danger');

                break;
            case 'info':
                $this->addClass('alert-info');

                break;
            case 'warning':
                $this->addClass('alert-warning');

                break;
            default:
                $this->addClass('alert-success');

                break;
        }
    }
}
