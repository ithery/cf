<?php

/**
 * Description of Exception
 *
 * @author Hery
 */

/**
 * Base class for all PHPUnit Framework exceptions.
 *
 * Ensures that exceptions thrown during a test run do not leave stray
 * references behind.
 *
 * Every Exception contains a stack trace. Each stack frame contains the 'args'
 * of the called function. The function arguments can contain references to
 * instantiated objects. The references prevent the objects from being
 * destructed (until test results are eventually printed), so memory cannot be
 * freed up.
 *
 * With enabled process isolation, test results are serialized in the child
 * process and unserialized in the parent process. The stack trace of Exceptions
 * may contain objects that cannot be serialized or unserialized (e.g., PDO
 * connections). Unserializing user-space objects from the child process into
 * the parent would break the intended encapsulation of process isolation.
 *
 * @see http://fabien.potencier.org/article/9/php-serialization-stack-traces-and-exceptions
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
class CQC_Exception extends RuntimeException {

    /**
     * @var array
     */
    protected $serializableTrace;

    public function __construct($message = '', $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);

        $this->serializableTrace = $this->getTrace();

        foreach (array_keys($this->serializableTrace) as $key) {
            unset($this->serializableTrace[$key]['args']);
        }
    }

    public function __toString() {
        $string = CQC_TestFailure::exceptionToString($this);

        if ($trace = CQC_Helper_Filter::getFilteredStacktrace($this)) {
            $string .= "\n" . $trace;
        }

        return $string;
    }

    /**
     * 
     * @return array
     */
    public function __sleep() {
        return array_keys(get_object_vars($this));
    }

    /**
     * Returns the serializable trace (without 'args').
     * @return array
     */
    public function getSerializableTrace() {
        return $this->serializableTrace;
    }

}
