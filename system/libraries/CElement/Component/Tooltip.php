<?php

class CElement_Component_Tooltip extends CElement_Component {
    use CTrait_Element_Property_Icon;

    /**
     * @var string|null
     */
    protected $text;

    /**
     * @param string|null $id
     *
     * @return void
     */
    public function __construct($id = null) {
        parent::__construct($id);
        $this->tag = 'i';
        $this->icon = c::theme('tooltip.icon', 'fas fa-info-circle');
        $clasess = c::theme('tooltip.class', '');
        $this->addClass($clasess);
        //<i data-tippy-content="Instance ID." class="fas fa-info-circle ml-1 wa-tooltip-sm" tabindex="0"></i>
    }

    /**
     * @return void
     */
    protected function build() {
        CManager::registerModule('tippy');
        $this->addClass($this->icon);
        $this->setAttr('data-tippy-content', $this->text);
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText($text) {
        $this->text = $text;

        return $this;
    }

    /**
     * @param string|null $id
     *
     * @return static
     */
    public static function factory($id = null) {
        // @phpstan-ignore-next-line
        return new static($id);
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        return " tippy && tippy(document.getElementById('" . $this->id . "'))";
    }
}
