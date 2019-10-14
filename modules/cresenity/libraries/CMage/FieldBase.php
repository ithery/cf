<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CMage_FieldBase {

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = true;

    /**
     * Indicates if the element should be shown on the detail view.
     *
     * @var bool
     */
    public $showOnDetail = true;

    /**
     * Indicates if the element should be shown on the creation view.
     *
     * @var bool
     */
    public $showOnAdd = true;

    /**
     * Indicates if the element should be shown on the update view.
     *
     * @var bool
     */
    public $showOnEdit = true;

    /**
     * Specify that the element should be hidden from the index view.
     *
     * @return $this
     */
    public function hideFromIndex() {
        $this->showOnIndex = false;

        return $this;
    }

    /**
     * Specify that the element should be hidden from the detail view.
     *
     * @return $this
     */
    public function hideFromDetail() {
        $this->showOnDetail = false;

        return $this;
    }

    /**
     * Specify that the element should be hidden from the creation view.
     *
     * @return $this
     */
    public function hideWhenAdd() {
        $this->showOnAdd = false;

        return $this;
    }

    /**
     * Specify that the element should be hidden from the update view.
     *
     * @return $this
     */
    public function hideWhenEdit() {
        $this->showOnEdit = false;

        return $this;
    }

    /**
     * Specify that the element should only be shown on the index view.
     *
     * @return $this
     */
    public function onlyOnIndex() {
        $this->showOnIndex = true;
        $this->showOnDetail = false;
        $this->showOnAdd = false;
        $this->showOnEdit = false;

        return $this;
    }

    /**
     * Specify that the element should only be shown on the detail view.
     *
     * @return $this
     */
    public function onlyOnDetail() {
        parent::onlyOnDetail();

        $this->showOnIndex = false;
        $this->showOnDetail = true;
        $this->showOnAdd = false;
        $this->showOnEdit = false;

        return $this;
    }

    /**
     * Specify that the element should only be shown on forms.
     *
     * @return $this
     */
    public function onlyOnForms() {
        $this->showOnIndex = false;
        $this->showOnDetail = false;
        $this->showOnAdd = true;
        $this->showOnEdit = true;

        return $this;
    }

    /**
     * Specify that the element should be hidden from forms.
     *
     * @return $this
     */
    public function exceptOnForms() {
        $this->showOnIndex = true;
        $this->showOnDetail = true;
        $this->showOnAdd = false;
        $this->showOnEdit = false;

        return $this;
    }

}
