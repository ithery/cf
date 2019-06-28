<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 12:11:07 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Javascript_Handler {

    /**
     *
     * @var CObservable_Javascript
     */
    protected $javascript;

    public function __construct($javascript) {
        $this->javascript = $javascript;
    }

    public function jquery() {
        return $this->javascript->jquery();
    }

    public function reload($options) {

        $ajaxOptions = array();
        $ajaxOptions['url'] = carr::get($options, 'url');
        $ajaxOptions['dataType'] = 'json';
        $ajaxOptions['data'] = array();
        $ajaxOptions['success'] = function($data) {
            $this->jquery()->html($data->html);
            $this->javascript->raw('eval($.cresenity.base64.decode(data.js));');
        };
        $ajaxOptions['error'] = function($jqXhr, $textStatus, $errorThrown) {
            $this->jquery()->html($jqXhr->statusText);
        };
        $this->javascript->raw('cresenity.reload(#'.$this->javascript->owner.',);');

        return $this;
    }

}
