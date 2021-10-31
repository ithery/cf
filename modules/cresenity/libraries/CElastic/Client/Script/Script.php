<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Inline script.
 *
 * @author avasilenko <aa.vasilenko@gmail.com>
 * @author Tobias Schultze <http://tobion.de>
 * @author Martin Janser <martin.janser@liip.ch>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/modules-scripting.html
 */
class CElastic_Client_Script_Script extends CElastic_Client_Script_AbstractScript {

    /**
     * @var string
     */
    private $_scriptCode;

    /**
     * @param string      $scriptCode Script source code
     * @param array|null  $params
     * @param string|null $lang
     * @param string|null $documentId Document ID the script action should be performed on (only relevant in update context)
     */
    public function __construct($scriptCode, array $params = null, $lang = null, $documentId = null) {
        parent::__construct($params, $lang, $documentId);
        $this->setScript($scriptCode);
    }

    /**
     * @param string $scriptCode
     *
     * @return $this
     */
    public function setScript($scriptCode) {
        $this->_scriptCode = $scriptCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getScript() {
        return $this->_scriptCode;
    }

    /**
     * {@inheritdoc}
     */
    protected function getScriptTypeArray() {
        return ['source' => $this->_scriptCode];
    }

}
