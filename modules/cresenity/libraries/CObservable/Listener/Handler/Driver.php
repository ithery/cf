<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 4:01:05 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CObservable_Listener_Handler_Driver implements CObservable_Listener_Handler_DriverInterface {

    use CTrait_Compat_Handler_Driver;

    /**
     * url for ajax handler type
     * @var string $url
     */
    protected $url;

    /**
     *
     * @var string $urlParam
     */
    protected $urlParam;

    /**
     * name of the driver
     * @var string
     */
    protected $name;

    /**
     * event from listener
     * @var string
     */
    protected $event;

    /**
     * id element of owner this event listener
     * @var string
     */
    protected $owner;

    /**
     * id of handler targeted renderable
     * @var string
     */
    protected $target;

    public function __construct($owner, $event, $name) {
        $this->name = $name;
        $this->event = $event;
        $this->owner = $owner;
        $this->url = "";
        $this->urlParam = array();
        $this->target = null;
    }

    public function setTarget($target) {
        if ($target instanceof CRenderable) {
            $target = $target->id();
        }
        $this->target = $target;

        return $this;
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function setOwner($owner) {
        $this->owner = $owner;
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
            $ajax_url = CAjaxMethod::factory()->set_type('handler_' . $this->name)
                    ->set_data('json', $this->content->json())
                    ->makeurl();
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

}
