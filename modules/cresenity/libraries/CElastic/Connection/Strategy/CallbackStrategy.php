<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 8:08:02 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Connection_Strategy_CallbackStrategy implements CElastic_Connection_Strategy_StrategyInterface {

    /**
     * @var callable
     */
    protected $_callback;

    /**
     * @param callable $callback
     *
     * @throws CElastic_Exception_InvalidException
     */
    public function __construct($callback) {
        if (!self::isValid($callback)) {
            throw new CElastic_Exception_InvalidException(sprintf('Callback should be a callable, %s given!', gettype($callback)));
        }
        $this->_callback = $callback;
    }

    /**
     * @param array|CElastic_Connection[] $connections
     *
     * @return CElastic_Connection
     */
    public function getConnection($connections) {
        return call_user_func_array($this->_callback, [$connections]);
    }

    /**
     * @param callable $callback
     *
     * @return bool
     */
    public static function isValid($callback) {
        return is_callable($callback);
    }

}
