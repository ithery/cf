<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 30, 2019, 3:38:37 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class ValidatingModel extends CModel_Query implements CValidation_MessageBagProviderInterface, CModel_Validating_ValidatingInterface {

    /**
     * Make model validate attributes.
     *
     * @see CModel_Validating_ValidatingTrait
     */
    use CModel_Validating_ValidatingTrait;

    /**
     * The default rules that the model will validate against.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Get the messages for the instance.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getMessageBag() {
        return $this->getErrors();
    }

}
