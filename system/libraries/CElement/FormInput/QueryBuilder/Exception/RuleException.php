<?php

/**
 * Thrown when a single rule is missing required fields (operator, id, field, type) or uses
 * an unrecognized operator. Caught internally so the offending rule is silently skipped.
 */
class CElement_FormInput_QueryBuilder_Exception_RuleException extends \Exception {
}
