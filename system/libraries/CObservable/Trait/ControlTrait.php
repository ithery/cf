<?php
/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 23, 2019, 11:43:39 PM
 */
trait CObservable_Trait_ControlTrait {
    /**
     * Create SelectSearch Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_SelectSearch
     */
    public function addSelectSearchControl($id = null) {
        $control = new CElement_FormInput_SelectSearch($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Text Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Text
     */
    public function addTextControl($id = null) {
        $control = new CElement_FormInput_Text($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Text Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Textarea
     */
    public function addTextareaControl($id = null) {
        $control = new CElement_FormInput_Textarea($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Hidden Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Hidden
     */
    public function addHiddenControl($id = null) {
        $control = new CElement_FormInput_Hidden($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Text Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Password
     */
    public function addPasswordControl($id = null) {
        $control = new CElement_FormInput_Password($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Select Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Select
     */
    public function addSelectControl($id = null) {
        $control = new CElement_FormInput_Select($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Select Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_AutoNumeric
     */
    public function addAutoNumericControl($id = null) {
        $control = new CElement_FormInput_AutoNumeric($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Select Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Date
     */
    public function addDateControl($id = null) {
        $control = new CElement_FormInput_Date($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Date Range Drop Down Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_DateRange_DropdownButton
     */
    public function addDateRangeDropdownButtonControl($id = null) {
        $control = new CElement_FormInput_DateRange_DropdownButton($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Date Range Drop Down Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Label
     */
    public function addLabelControl($id = null) {
        $control = new CElement_FormInput_Label($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Date Range Drop Down Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Number
     */
    public function addNumberControl($id = null) {
        $control = new CElement_FormInput_Number($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Date Range Drop Down Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Email
     */
    public function addEmailControl($id = null) {
        $control = new CElement_FormInput_Email($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Multiple Image Ajax Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_MultipleImageAjax
     */
    public function addMultipleImageAjaxControl($id = null) {
        $control = new CElement_FormInput_MultipleImageAjax($id);
        $this->wrapper->add($control);

        return $control;
    }
}
