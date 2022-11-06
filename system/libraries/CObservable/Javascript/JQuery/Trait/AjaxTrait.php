<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 2, 2018, 10:38:57 PM
 */
trait CObservable_Javascript_JQuery_Trait_AjaxTrait {
    /**
     * @param array $options
     *
     * @return static
     */
    public function ajax($options = []) {
        /** @var CObservable_Javascript_JQuery $this */
        // $success = carr::get($options, 'success', null);
        // if ($success != null) {
        //     if ($success instanceof Closure) {
        //         $data = new CJavascript_Mock_Variable('data');
        //         $success = $this->javascript->runClosure($success, $data);
        //     }
        // }

        // //error
        // $error = carr::get($options, 'error', null);
        // if ($error != null) {
        //     if ($error instanceof Closure) {
        //         $jqXHR = new CJavascript_Mock_Variable('jqXhr');
        //         $textStatus = new CJavascript_Mock_Variable('textStatus');
        //         $errorThrown = new CJavascript_Mock_Variable('errorThrown');
        //         $error = $this->javascript->runClosure($error, $jqXHR, $textStatus, $errorThrown);
        //     }
        // }

        // $this->filterArgs($success);
        // $this->filterArgs($error);
        // $options['success'] = $success;
        // $options['error'] = $error;

        $this->jQueryStatement()->ajax($options);
        $this->resetJQueryStatement();

        return $this;
    }
}
