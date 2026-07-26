<?php
/**
 * @see CElement_Component_DataTable
 */
trait CElement_Component_DataTable_Trait_FooterTrait {
    /**
     * @var null|string
     */
    public $footerTitle;

    /**
     * @var null|bool
     */
    public $footer;

    /**
     * @var null|CElement_Component_DataTable_FooterField[]
     */
    public $footerFields;

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

    /**
     * @param null|string $label
     * @param mixed       $value
     * @param string      $align
     * @param int         $labelColSpan
     *
     * @return $this
     */
    public function addFooterField($label = null, $value = null, $align = 'left', $labelColSpan = 0) {
        $footerField = new CElement_Component_DataTable_FooterField();

        $footerField->setLabel($label)->setValue($value)->setAlign($align)->setLabelColSpan($labelColSpan);

        $this->footerFields[] = $footerField;

        return $this;
    }

    /**
     * @return CElement_Component_DataTable_FooterField[]
     */
    public function getFooterFields() {
        return  $this->footerFields;
    }
}
