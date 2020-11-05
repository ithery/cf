<?php

/**
 * Description of IsJson
 *
 * @author Hery
 */

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class CQC_UnitTest_Constraint_String_IsJson extends CQC_UnitTest_ConstraintAbstract {

    /**
     * Returns a string representation of the constraint.
     */
    public function toString() {
        return 'is valid JSON';
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $other value or object to evaluate
     * @return bool
     */
    protected function matches($other) {
        if ($other === '') {
            return false;
        }

        json_decode($other);

        if (json_last_error()) {
            return false;
        }

        return true;
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other evaluated value or object
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @return string
     */
    protected function failureDescription($other) {
        if ($other === '') {
            return 'an empty string is valid JSON';
        }

        json_decode($other);
        $error = (string) CQC_UnitTest_Constraint_JsonMatchesErrorMessageProvider::determineJsonError(
                        (string) json_last_error()
        );

        return sprintf(
                '%s is valid JSON (%s)', $this->exporter()->shortenedExport($other), $error
        );
    }

}
