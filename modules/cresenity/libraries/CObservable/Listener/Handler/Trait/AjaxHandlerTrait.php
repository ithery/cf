<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 17, 2019, 11:23:04 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CObservable_Listener_Handler_Trait_AjaxHandlerTrait {

    protected $method;

    /**
     * url for ajax handler type
     * @var string $url
     */
    protected $url;

    /**
     *
     * @var string $urlParam
     */
    protected $urlParam = array();

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function setUrlParam($urlParam) {
        if (!is_array($urlParam)) {
            trigger_error('Invalid URL Param ' . cdbg::var_dump($urlParam, true) . '');
        }
        $this->urlParam = $urlParam;
        return $this;
    }

    public function addUrlParam($k, $urlParam) {
        $this->urlParam[$k] = $url_param;
        return $this;
    }

    public function generatedUrl() {
        $link = $this->url;

        if (strlen($link) == 0) {
            $ajax_url = CAjax::createMethod()->setType('handler_' . $this->name)
                    ->setData('json', $this->content->json())
                    ->makeUrl();
            $link = $ajax_url;
        }

        foreach ($this->urlParam as $k => $p) {
            preg_match_all("/{([\w]*)}/", $link, $matches, PREG_SET_ORDER);
            foreach ($matches as $val) {
                $str = $val[1]; //matches str without bracket {}
                $b_str = $val[0]; //matches str with bracket {}
                if ($k == $str) {
                    $link = str_replace($b_str, $p, $link);
                }
            }
        }
        return $link;
    }

    public function onSuccessListener() {
        if (!isset($this->handlerListeners['ajaxSuccess'])) {
            $this->handlerListeners['ajaxSuccess'] = new CObservable_Listener_Pseudo_SuccessListener($this);
        }
        return $this->handlerListeners['ajaxSuccess'];
    }

    public function onErrorListener() {
        if (!isset($this->handlerListeners['ajaxError'])) {
            $this->handlerListeners['ajaxError'] = new CObservable_Listener_Pseudo_ErrorListener($this);
        }
        return $this->handlerListeners['ajaxError'];
    }

    public function onCompleteListener() {
        if (!isset($this->handlerListeners['ajaxComplete'])) {
            $this->handlerListeners['ajaxComplete'] = new CObservable_Listener_Pseudo_CompleteListener($this);
        }
        return $this->handlerListeners['ajaxComplete'];
    }

    public function haveSuccessListener() {
        return $this->haveListener('ajaxSuccess');
    }

    public function haveErrorListener() {
        return $this->haveListener('ajaxError');
        return isset($this->handlerListeners['ajaxError']);
    }

    public function haveCompleteListener() {
        return $this->haveListener('ajaxComplete');
    }

    public function getCompleteListener() {
        return $this->getListener('ajaxComplete');
    }

    public function getSuccessListener() {
        return $this->getListener('ajaxSuccess');
    }

    public function getErrorListener() {
        return $this->getListener('ajaxError');
    }

}
