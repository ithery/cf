<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 30, 2019, 3:33:01 PM
 */
class CModel_Validating_ValidationException extends CValidation_Exception implements CBase_MessageProviderInterface {
    /**
     * The model with validation errors.
     *
     * @var CModel
     */
    protected $model;

    /**
     * Create a new validation exception instance.
     *
     * @param CValidation_Validator $validator
     * @param CModel                $model
     *
     * @return void
     */
    public function __construct(CValidation_Validator $validator, CModel $model) {
        parent::__construct($validator);

        $this->model = $model;
    }

    /**
     * Get the mdoel with validation errors.
     *
     * @return CModel
     */
    public function model() {
        return $this->model;
    }

    /**
     * Get the mdoel with validation errors.
     *
     * @return CModel
     */
    public function getModel() {
        return $this->model();
    }

    /**
     * Get the validation errors.
     *
     * @return CBase_Messagebag
     */
    public function errors() {
        return $this->validator->errors();
    }

    /**
     * Get the validation errors.
     *
     * @return CBase_Messagebag
     */
    public function getErrors() {
        return $this->errors();
    }

    /**
     * Get the messages for the instance.
     *
     * @return CBase_Messagebags
     */
    public function getMessageBag() {
        return $this->errors();
    }
}
