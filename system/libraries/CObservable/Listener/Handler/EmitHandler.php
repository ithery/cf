<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 20, 2019, 3:27:04 PM
 */
class CObservable_Listener_Handler_EmitHandler extends CObservable_Listener_Handler {
    protected $method;

    protected $parameters;

    public function __construct($listener) {
        parent::__construct($listener);

        $this->name = 'Emit';
    }

    /**
     * Set Method
     *
     * @param string $method
     *
     * @return $this
     */
    public function setMethod($method) {
        $this->method = $method;

        return $this;
    }

    /**
     * Add Emit Parameter
     *
     * @param mixed $param
     *
     * @return $this
     */
    public function addParameter($param) {
        $this->parameters[] = $param;
    }

    /**
     * Set Emit Parameters
     *
     * @param array $params
     *
     * @return $this
     */
    public function setParameters(array $params) {
        $this->parameters = $params;
    }

    public function js() {
        $js = '';
        if ($this->method) {
            $js .= 'window.cresenity.ui.emit(' . c::e($this->method) . "'";

            if (is_array($this->parameters) && count($this->parameters) > 0) {
                foreach ($this->parameters as $param) {
                    $js .= ',' . json_encode($param);
                }
            }
            $js .= ');';
        }

        return $js;
    }
}
