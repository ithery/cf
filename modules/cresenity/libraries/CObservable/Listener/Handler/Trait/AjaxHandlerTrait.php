<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 17, 2019, 11:23:04 PM
 */
trait CObservable_Listener_Handler_Trait_AjaxHandlerTrait {
    protected $method;

    /**
     * Url for ajax handler type
     *
     * @var string $url
     */
    protected $url;

    /**
     * @var array $urlParam
     */
    protected $urlParam = [];

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
        $this->urlParam[$k] = $urlParam;
        return $this;
    }

    public function generatedUrl() {
        $link = $this->url;

        if (strlen($link) == 0) {
            $ajaxUrl = CAjax::createMethod()->setType('handler_' . $this->name)
                ->setData('json', $this->content->json())
                ->makeUrl();
            $link = $ajaxUrl;
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

    /**
     * Add Ajax Success Listener
     *
     * @return CObservable_Listener_Pseudo_SuccessListener
     */
    public function onSuccessListener() {
        if (!isset($this->handlerListeners['ajaxSuccess'])) {
            $this->handlerListeners['ajaxSuccess'] = new CObservable_Listener_Pseudo_SuccessListener($this);
        }
        return $this->handlerListeners['ajaxSuccess'];
    }

    /**
     * Add Ajax Error Listener
     *
     * @return CObservable_Listener_Pseudo_ErrorListener
     */
    public function onErrorListener() {
        if (!isset($this->handlerListeners['ajaxError'])) {
            $this->handlerListeners['ajaxError'] = new CObservable_Listener_Pseudo_ErrorListener($this);
        }
        return $this->handlerListeners['ajaxError'];
    }

    /**
     * Add Ajax Complete Listener
     *
     * @return CObservable_Listener_Pseudo_CompleteListener
     */
    public function onCompleteListener() {
        if (!isset($this->handlerListeners['ajaxComplete'])) {
            $this->handlerListeners['ajaxComplete'] = new CObservable_Listener_Pseudo_CompleteListener($this);
        }
        return $this->handlerListeners['ajaxComplete'];
    }

    /**
     * Check have success listener
     *
     * @return bool
     */
    public function haveSuccessListener() {
        return $this->haveListener('ajaxSuccess');
    }

    /**
     * Check have error listener
     *
     * @return bool
     */
    public function haveErrorListener() {
        return $this->haveListener('ajaxError');
        return isset($this->handlerListeners['ajaxError']);
    }

    /**
     * Check have complete listener
     *
     * @return bool
     */
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

    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }
}
