<?php

interface CApi_Contract_MessageBagErrorsInterface {
    /**
     * Get the errors message bag.
     *
     * @return \CBase_MessageBag
     */
    public function getErrors();

    /**
     * Determine if message bag has any errors.
     *
     * @return bool
     */
    public function hasErrors();
}
