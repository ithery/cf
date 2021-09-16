<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 4:01:05 PM
 */
abstract class CObservable_Listener_Handler_Driver implements CObservable_Listener_Handler_DriverInterface {
    use CTrait_Compat_Handler_Driver;

    /**
     * Url for ajax handler type
     *
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $urlParam;

    /**
     * Name of the driver
     *
     * @var string
     */
    protected $name;

    /**
     * Event from listener
     *
     * @var string
     */
    protected $event;

    /**
     * Id element of owner this event listener
     *
     * @var string
     */
    protected $owner;

    /**
     * Id of handler targeted renderable
     *
     * @var string
     */
    protected $target;

    public function __construct($owner, $event, $name) {
        $this->name = $name;
        $this->event = $event;
        $this->owner = $owner;
        $this->url = '';
        $this->urlParam = [];
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
            trigger_error('Invalid URL Param ' . cdbg::varDump($urlParam, true) . '');
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
