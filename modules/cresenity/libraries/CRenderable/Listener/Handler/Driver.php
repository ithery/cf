<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 4:01:05 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CRenderable_Listener_Handler_Driver {

    use CTrait_Compat_Handler_Driver;

    /**
     *
     * @var url for ajax handler type
     */
    protected $url;

    /**
     *
     * @var name of the driver
     */
    protected $name;

    /**
     *
     * @var event from listener
     */
    protected $event;
    protected $owner;
    protected $url_param;

    /**
     *
     * @var id of handler targeted renderable
     */
    protected $target;

    public function __construct($owner, $event, $name) {
        $this->name = $name;
        $this->event = $event;
        $this->owner = $owner;
        $this->url = "";
        $this->url_param = array();
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

    public function set_owner($owner) {
        $this->owner = $owner;
        return $this;
    }

    public function set_url_param($url_param) {
        if (!is_array($url_param)) {
            trigger_error('Invalid URL Param ' . cdbg::var_dump($url_param, true) . '');
        }
        $this->url_param = $url_param;
        return $this;
    }

    public function add_url_param($k, $url_param) {
        $this->url_param[$k] = $url_param;
        return $this;
    }

    public function generated_url() {
        $link = $this->url;

        if (strlen($link) == 0) {
            $ajax_url = CAjaxMethod::factory()->set_type('handler_' . $this->name)
                    ->set_data('json', $this->content->json())
                    ->makeurl();
            $link = $ajax_url;
        }

        foreach ($this->url_param as $k => $p) {
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

    protected function script() {
        if (strlen($this->target) == 0) {
            
        }
        $js = "";
        return $js;
    }

}
