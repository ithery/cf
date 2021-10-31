<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Container for scripts as fields.
 *
 * @author Sebastien Lavoie <github@lavoie.sl>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-script-fields.html
 */
class CElastic_Client_Script_ScriptFields extends CElastic_Param {

    /**
     * @param \Elastica\Script\Script[]|array $scripts OPTIONAL
     */
    public function __construct(array $scripts = []) {
        if ($scripts) {
            $this->setScripts($scripts);
        }
    }

    /**
     * @param string                                $name   Name of the Script field
     * @param CElastic_Client_Script_AbstractScript $script
     *
     * @throws CElastic_Exception_InvalidException
     *
     * @return $this
     */
    public function addScript($name, CElastic_Client_Script_AbstractScript $script) {
        if (!is_string($name) || !strlen($name)) {
            throw new CElastic_Exception_InvalidException('The name of a Script is required and must be a string');
        }
        $this->setParam($name, $script);
        return $this;
    }

    /**
     * @param \Elastica\Script\Script[]|array $scripts Associative array of string => Elastica\Script\Script
     *
     * @return $this
     */
    public function setScripts(array $scripts) {
        $this->_params = [];
        foreach ($scripts as $name => $script) {
            $this->addScript($name, $script);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        return $this->_convertArrayable($this->_params);
    }

}
