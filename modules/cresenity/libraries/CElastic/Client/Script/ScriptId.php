<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Stored script referenced by ID.
 *
 * @author Tobias Schultze <http://tobion.de>
 * @author Martin Janser <martin.janser@liip.ch>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/modules-scripting.html
 */
class CElastic_Client_Script_ScriptId extends CElastic_Client_Script_AbstractScript {

    /**
     * @var string
     */
    private $_scriptId;

    /**
     * @param string      $scriptId   Script ID
     * @param array|null  $params
     * @param string|null $lang
     * @param string|null $documentId Document ID the script action should be performed on (only relevant in update context)
     */
    public function __construct($scriptId, array $params = null, $lang = null, $documentId = null) {
        parent::__construct($params, $lang, $documentId);
        $this->setScriptId($scriptId);
    }

    /**
     * @param string $scriptId
     *
     * @return $this
     */
    public function setScriptId($scriptId) {
        $this->_scriptId = $scriptId;
        return $this;
    }

    /**
     * @return string
     */
    public function getScriptId() {
        return $this->_scriptId;
    }

    /**
     * {@inheritdoc}
     */
    protected function getScriptTypeArray() {
        return ['id' => $this->_scriptId];
    }

}
