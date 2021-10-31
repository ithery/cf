<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 4:36:46 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Facade for a specific DSL object.
 *
 * @author Manuel Andreo Garcia <andreo.garcia@googlemail.com>
 * */
class CElastic_Client_QueryBuilder_Facade {

    /**
     * @var DSL
     */
    private $_dsl;

    /**
     * @var Version
     */
    private $_version;

    /**
     * Constructor.
     *
     * @param DSL     $dsl
     * @param Version $version
     */
    public function __construct(CElastic_Client_QueryBuilder_DSL $dsl, CElastic_Client_QueryBuilder_Version $version) {
        $this->_dsl = $dsl;
        $this->_version = $version;
    }

    /**
     * Executes DSL methods.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @throws QueryBuilderException
     *
     * @return mixed
     */
    public function __call($name, array $arguments) {
        // defined check
        if (false === method_exists($this->_dsl, $name)) {
            throw new CElastic_Exception_QueryBuilderException(
            'undefined ' . $this->_dsl->getType() . ' "' . $name . '"'
            );
        }
        // version support check
        if (false === $this->_version->supports($name, $this->_dsl->getType())) {
            $reflection = new \ReflectionClass($this->_version);
            throw new CElastic_Exception_QueryBuilderException(
            $this->_dsl->getType() . ' "' . $name . '" in ' . $reflection->getShortName() . ' not supported'
            );
        }
        return call_user_func_array([$this->_dsl, $name], $arguments);
    }

}
