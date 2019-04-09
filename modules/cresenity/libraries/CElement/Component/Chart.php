<?php

/**
 * 
 */
abstract class CElement_Component_Chart extends CElement_Component
{
    protected $type;
    protected $labels;
    protected $data;
    protected $width;
    protected $height;

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

    public function setLabels(array $labels)
    {
        $this->labels = $labels;
        return $this;
    }

    public function addData(array $data, $label = null)
    {
        $this->data[] = [
            'data' => $data,
            'label' => $label,
        ];
        return $this;
    }

    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    protected function getColor($color = null, $opacity = 1.0)
    {
        if (! $color) {
            return 'rgba(' . mt_rand(0, 255) . ', ' . mt_rand(0, 255) . ', ' . mt_rand(0, 255) . ', ' . $opacity . ')';
        } else {
            preg_match_all("([\d\.]+)", $color, $matches);
            $opacity = $opacity ?: $matches[0][3];
            return 'rgba(' . $matches[0][0] . ', ' . $matches[0][1] . ', ' . $matches[0][2] . ', ' . $opacity . ')';
        }
    }
}