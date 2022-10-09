<?php

interface CJavascript_PhpJs_Contract_SourceWriterInterface {
    /**
     * @param null $atStart
     *
     * @return CJavascript_PhpJs_Contract_SourceWriterInterface
     */
    public function pushDelay($atStart = null);

    /**
     * @param null $id
     *
     * @return CJavascript_PhpJs_Contract_SourceWriterInterface
     */
    public function popDelay(&$id = null);

    /**
     * @param $var
     *
     * @return CJavascript_PhpJs_Contract_SourceWriterInterface
     */
    public function popDelayToVar(&$var);

    /**
     * @param $id
     *
     * @return CJavascript_PhpJs_Contract_SourceWriterInterface
     */
    public function writeDelay($id);

    /**
     * @return CJavascript_PhpJs_Contract_SourceWriterInterface
     */
    public function writeLastDelay();

    /**
     * @param $string
     * @param ... $objects
     *
     * @return CJavascript_PhpJs_Contract_SourceWriterInterface
     */
    public function println($string = '', $objects = null);

    /**
     * @param $string
     * @param ... $objects
     *
     * @return CJavascript_PhpJs_Contract_SourceWriterInterface
     */
    public function print($string, $objects = null);

    /**
     * @return CJavascript_PhpJs_Contract_SourceWriterInterface
     */
    public function indent();

    /**
     * @return CJavascript_PhpJs_Contract_SourceWriterInterface
     */
    public function outdent();

    /**
     * @param $string
     * @param ... $objects
     *
     * @return CJavascript_PhpJs_Contract_SourceWriterInterface
     */
    public function indentln($string, $objects = null);
}
