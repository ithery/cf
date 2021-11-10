<?php

trait CElement_Component_DataTable_Trait_FooterTrait {
    public $footerTitle;

    public $footer;

    public $footerField;

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setFooterTitle($title) {
        $this->footerTitle = $title;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setFooter($bool) {
        $this->footer = $bool;

        return $this;
    }

    public function addFooterField($label, $value, $align = 'left', $labelcolspan = 0) {
        $f = [
            'label' => $label,
            'value' => $value,
            'align' => $align,
            'labelcolspan' => $labelcolspan,
        ];
        $this->footerField[] = $f;

        return $this;
    }
}
