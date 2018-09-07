<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Base class for Script object.
 *
 * Wherever scripting is supported in the Elasticsearch API, scripts can be referenced as "inline", "id" or "file".
 *
 * @author Nicolas Assing <nicolas.assing@gmail.com>
 * @author Tobias Schultze <http://tobion.de>
 * @author Martin Janser <martin.janser@liip.ch>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/modules-scripting.html
 */
abstract class CElastic_Client_Script_AbstractScript extends CElastic_Client_AbstractUpdateAction {

    const LANG_MOUSTACHE = 'moustache';
    const LANG_EXPRESSION = 'expression';
    const LANG_PAINLESS = 'painless';

    /**
     * @var string
     */
    private $_lang;

    /**
     * Factory to create a script object from data structure (reverse toArray).
     *
     * @param string|array|CElastic_Client_Script_AbstractScript $data
     *
     * @throws CElastic_Exception_InvalidException
     *
     * @return CElastic_Client_Script_Script|CElastic_Client_Script_ScriptId
     */
    public static function create($data) {
        if ($data instanceof self) {
            return $data;
        }
        if (is_array($data)) {
            return self::_createFromArray($data);
        }
        if (is_string($data)) {
            $class = self::class === get_called_class() ? CElastic_Client_Script_Script::class : get_called_class();
            return new $class($data);
        }
        throw new CElastic_Exception_InvalidException('Failed to create script. Invalid data passed.');
    }

    private static function _createFromArray(array $data) {
        $params = carr::path($data,'script.params',[]);
        $lang = carr::path($data,'script.lang',null);
        if (!is_array($params)) {
            throw new CElastic_Exception_InvalidException('Script params must be an array');
        }
        if (isset($data['script']['source'])) {
            return new CElastic_Client_Script_Script(
                    $data['script']['source'], $params, $lang
            );
        }
        if (isset($data['script']['id'])) {
            return new CElastic_Client_Script_ScriptId(
                    $data['script']['id'], $params, $lang
            );
        }
        throw new CElastic_Exception_InvalidException('Failed to create script. Invalid data passed.');
    }

    /**
     * @param array|null  $params
     * @param string|null $lang       Script language, see constants
     * @param string|null $documentId Document ID the script action should be performed on (only relevant in update context)
     */
    public function __construct(array $params = null, $lang = null, $documentId = null) {
        if ($params) {
            $this->setParams($params);
        }
        if (null !== $lang) {
            $this->setLang($lang);
        }
        if (null !== $documentId) {
            $this->setId($documentId);
        }
    }

    /**
     * @param string $lang
     *
     * @return $this
     */
    public function setLang($lang) {
        $this->_lang = $lang;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLang() {
        return $this->_lang;
    }

    /**
     * Returns an array with the script type as key and the script content as value.
     *
     * @return array
     */
    abstract protected function getScriptTypeArray();

    /**
     * {@inheritdoc}
     */
    public function toArray() {
        $array = $this->getScriptTypeArray();
        if (!empty($this->_params)) {
            $array['params'] = $this->_convertArrayable($this->_params);
        }
        if (null !== $this->_lang) {
            $array['lang'] = $this->_lang;
        }
        return ['script' => $array];
    }

}
