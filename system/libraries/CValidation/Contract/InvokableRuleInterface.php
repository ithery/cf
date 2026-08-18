<?php

/**
 * @deprecated see CValidation_Contract_ValidationRuleInterface
 */
interface CValidation_Contract_InvokableRuleInterface {
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     *
     * @return void
     */
    public function __invoke(string $attribute, mixed $value, Closure $fail);
}
