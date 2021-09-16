<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 30, 2019, 3:27:37 PM
 */
trait CModel_Validating_Injector_UniqueWithInjectorTrait {
    /**
     * Prepare a unique_with rule, adding the model identifier if required.
     *
     * @param array  $parameters
     * @param string $field
     *
     * @return string
     */
    protected function prepareUniqueWithRule($parameters, $field) {
        // Table and intermediary fields are required for this validator to work and cannot be guessed.
        // Let's just check the model identifier.
        if ($this->exists) {
            // If the identifier isn't set, add it.
            if (count($parameters) < 3 || !preg_match('/^\d+(\s?=\s?\w*)?$/', c::last($parameters))) {
                $parameters[] = $this->getModel()->getKey();
            }
        }
        return 'unique_with:' . implode(',', $parameters);
    }
}
