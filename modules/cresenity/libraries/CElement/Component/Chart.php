<?php

/**
 * 
 */
abstract class CElement_Component_Chart extends CElement_Component
{
    protected $type;
    protected $data;
    protected $animation;
    protected $padding;
    protected $legend;
    protected $title;
    protected $tooltip;
    protected $pointStyle;
    protected $width;
    protected $height;

    public function __construct($id = "")
    {
        parent::__construct($id);
        $this->setTag('canvas');
        $this->type = 'line';
        $this->data = [];
        $this->width = 500;
        $this->height = 500;
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
        $this->data = $data;
        return $this;
    }

    public function setLabels(array $labels)
    {
        if (! isset($this->data['labels'])) {
            $this->data['labels'] = [];
        }

        $this->data['labels'] = $labels;
        return $this;
    }

    public function addDataset(array $data, $label = null, $fill = false, $borderColor = null)
    {
        if (! isset($this->data['datasets'])) {
            $this->data['datasets'] = [];
        }

        $dataset = [];
        $dataset['data'] = $data;
        $dataset['borderColor'] = $borderColor ?: '#' . str_pad(dechex(mt_rand(0x0, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
        $dataset['fill'] = $fill;
        if ($label) {
            $dataset['label'] = $label;
        }

        $this->data['datasets'][] = $dataset;
        return $this;
    }

    public function setAnimation($animation)
    {
        $this->animation = $animation;
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
}