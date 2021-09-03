<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 3, 2018, 1:26:28 AM
 */
class CJavascript_Statement_JQuery_Ajax implements CJavascript_Statement_JQuery_CompilableInterface {
    protected $method = 'GET';

    protected $url = null;

    protected $complete = null;

    protected $dataType = 'json';

    protected $data = null;

    protected $success = null;

    protected $error = null;

    public function __construct($options) {
        $this->method = carr::get($options, 'method', 'GET');
        $this->url = carr::get($options, 'url', curl::base());
        $this->dataType = carr::get($options, 'dataType', 'json');
        $this->data = carr::get($options, 'data', null);

        $this->complete = carr::get($options, 'complete', null);
        $this->success = carr::get($options, 'success', null);
        $this->error = carr::get($options, 'error', null);

        if ($this->error == null) {
            $this->error = carr::get($options, 'fail', null);
        }
    }

    public function compileAjaxEvent($statement, $args = []) {
        if (is_array($statement)) {
            $function = new CJavascript_Statement_Function('', $args);
            foreach ($statement as $stat) {
                $function->addStatement($stat);
            }
            $statement = $function;
        }
        if ($statement instanceof CJavascript_Statement && (!$statement instanceof CJavascript_Statement_Function)) {
            $function = new CJavascript_Statement_Function('', $args);
            $function->addStatement($statement);
            $statement = $function;
        }
        if ($statement instanceof CJavascript_Statement_Function) {
            $statement = $statement->getStatement();
        }

        return $statement;
    }

    public function compile() {
        $str = '.ajax({';
        $str .= 'url:' . CJavascript_Helper_Javascript::prepValue($this->url) . ',';
        $str .= 'dataType:' . CJavascript_Helper_Javascript::prepValue($this->dataType) . ',';

        if ($this->complete != null) {
            $args = ['data'];
            $this->complete = $this->compileAjaxEvent($this->complete, $args);
            $str .= 'complete:' . $this->complete . ',';
        }
        if ($this->success != null) {
            $args = ['data'];
            $this->success = $this->compileAjaxEvent($this->success, $args);
            $str .= 'success:' . $this->success . ',';
        }
        if ($this->error != null) {
            $args = ['jqXhr', 'textStatus', 'errorThrown'];
            $this->error = $this->compileAjaxEvent($this->error, $args);
            $str .= 'error:' . $this->error . ',';
        }

        $str .= 'method:' . CJavascript_Helper_Javascript::prepValue($this->method);
        $str .= '})';
        return $str;
    }
}
