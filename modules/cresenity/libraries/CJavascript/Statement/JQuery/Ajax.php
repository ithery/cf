<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 3, 2018, 1:26:28 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJavascript_Statement_JQuery_Ajax implements CJavascript_Statement_JQuery_CompilableInterface {

    protected $method = 'GET';
    protected $url = null;
    protected $complete = null;
    protected $success = null;
    protected $error = null;

    public function __construct($options) {
        $this->method = carr::get($options, 'method', 'GET');
        $this->url = carr::get($options, 'url', curl::base());

        $this->complete = carr::get($options, 'complete', null);
        $this->success = carr::get($options, 'success', null);
        $this->error = carr::get($options, 'error', null);
        if ($this->error == null) {
            $this->error = carr::get($options, 'fail', null);
        }
    }

    public function compileAjaxEvent($statement) {
        if (is_array($statement)) {
            $function = new CJavascript_Statement_Function('', array('data'));
            foreach ($statement as $stat) {
                $function->addStatement($stat);
            }
            $statement = $function;
        }
        if ($statement instanceOf CJavascript_Statement && (!$statement instanceOf CJavascript_Statement_Function)) {
            $function = new CJavascript_Statement_Function('', array('data'));
            $function->addStatement($statement);
            $statement = $function;
        }
        if ($statement instanceOf CJavascript_Statement_Function) {
            $statement = $statement->getStatement();
        }

        return $statement;
    }

    public function compile() {
        $str = '.ajax({';
        $str .= 'url:' . CJavascript_Helper_Javascript::prepValue($this->url) . ',';

        if ($this->complete != null) {
            $this->complete = $this->compileAjaxEvent($this->complete);
            $str .= 'complete:' . $this->complete . ',';
        }
        if ($this->success != null) {
            $this->success = $this->compileAjaxEvent($this->success);
            $str .= 'success:' . $this->success . ',';
        }

        $str .= 'method:' . CJavascript_Helper_Javascript::prepValue($this->method);
        $str .= '})';
        return $str;
    }

}
