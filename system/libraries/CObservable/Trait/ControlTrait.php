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
     * Create Map Picker Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_MapPicker
     */
    public function addMapPickerControl($id = null) {
        $control = new CElement_FormInput_MapPicker($id);
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
     * @return CElement_FormInput_Range
     */
    public function addRangeControl($id = null) {
        $control = new CElement_FormInput_Range($id);
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
     * Create Text Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Textarea_Summernote
     */
    public function addSummerNoteControl($id = null) {
        $control = new CElement_FormInput_Textarea_Summernote($id);
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
     * Create Csrf Hidden Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Csrf
     */
    public function addCsrfControl($id = null) {
        $control = new CElement_FormInput_Csrf($id);
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
     * Create Select Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Time
     */
    public function addTimeControl($id = null) {
        $control = new CElement_FormInput_Time($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Date Time Modal Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_DateTime_ModalDateTime
     */
    public function addDateTimeModalControl($id = null) {
        $control = new CElement_FormInput_DateTime_ModalDateTime($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Date Time Modal Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_DateTime_MaterialDateTime
     */
    public function addDateTimeMaterialControl($id = null) {
        $control = new CElement_FormInput_DateTime_MaterialDateTime($id);
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
     * Create Image Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_File
     */
    public function addFileControl($id = null) {
        $control = new CElement_FormInput_File($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Image Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Image
     */
    public function addImageControl($id = null) {
        $control = new CElement_FormInput_Image($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Image Ajax Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_ImageAjax
     */
    public function addImageAjaxControl($id = null) {
        $control = new CElement_FormInput_ImageAjax($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Image Ajax Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_FileAjax
     */
    public function addFileAjaxControl($id = null) {
        $control = new CElement_FormInput_FileAjax($id);
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

    /**
     * Create Multiple Image Ajax Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_MultipleFileAjax
     */
    public function addMultipleFileAjaxControl($id = null) {
        $control = new CElement_FormInput_MultipleFileAjax($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Block Editor Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_EditorJs
     */
    public function addEditorJsControl($id = null) {
        $control = new CElement_FormInput_EditorJs($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Radio Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Radio
     */
    public function addRadioControl($id = null) {
        $control = new CElement_FormInput_Radio($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Radio List Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_RadioList
     */
    public function addRadioListControl($id = null) {
        $control = new CElement_FormInput_RadioList($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Checkbox Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Checkbox
     */
    public function addCheckboxControl($id = null) {
        $control = new CElement_FormInput_Checkbox($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Checkbox List Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_CheckboxList
     */
    public function addCheckboxListControl($id = null) {
        $control = new CElement_FormInput_CheckboxList($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Mini Color Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_MiniColor
     */
    public function addMiniColorControl($id = null) {
        $control = new CElement_FormInput_MiniColor($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Query Builder Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_QueryBuilder
     */
    public function addQueryBuilderControl($id = null) {
        $control = new CElement_FormInput_QueryBuilder($id);
        $this->wrapper->add($control);

        return $control;
    }

    /**
     * Create Sortable Control.
     *
     * @param null|string $id
     *
     * @return CElement_FormInput_Sortable
     */
    public function addSortableControl($id = null) {
        $control = new CElement_FormInput_Sortable($id);
        $this->wrapper->add($control);

        return $control;
    }
}
