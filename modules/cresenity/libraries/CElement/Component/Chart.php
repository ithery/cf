<?php

/**
 * 
 */
abstract class CElement_Component_Chart extends CElement_Component
{
    private $type;
    private $data;
    private $animation;
    private $padding;
    private $legend;
    private $title;
    private $tooltip;
    private $pointStyle;

    public function __construct($id = "")
    {
        parent::__construct($id);

        $this->type = 'line';
        $this->data = [];
    }

    public static function factory($type, $id = "")
    {
        $className = 'CElement_Component_Chart_' . ucfirst(strtolower($type));
        return new $className($id);
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setData(array $data)
    {
        $this->data = json_encode($data);
        return $this;
    }

    public function setAnimation($animation)
    {
        $this->animation = $animation;
        return $this;
    }
}