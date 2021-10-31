<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 30, 2019, 3:38:37 PM
 */
abstract class ValidatingModel extends CModel_Query implements CBase_MessageProviderInterface, CModel_Validating_ValidatingInterface {
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
     * @return CBase_MessageBag
     */
    public function getMessageBag() {
        return $this->getErrors();
    }
}
