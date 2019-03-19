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
        $this->data = '{}';
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

    public function setData(array $val)
    {
        $data = [];
        $data['labels'] = ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'];
        $data['datasets'] = [];
        $data['datasets'][0] = [];
        $data['datasets'][0]['label'] = '# of Votes';
        $data['datasets'][0]['data'] = [];
        $data['datasets'][0]['backgroundColor'] = [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ];
        $data['datasets'][0]['borderColor'] = [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ];
        $data['datasets'][0]['borderWidth'] = 1;

        foreach ($val as $k => $v) {
            $data['datasets'][0]['data'][] = $v;
        }

        $this->data = json_encode($data);
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