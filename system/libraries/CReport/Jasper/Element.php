<?php

class CReport_Jasper_Element {
    /**
     * @var SimpleXMLElement
     */
    public $xmlElement;

    public $children;

    protected $name;

    private $properties;

    public function __construct(SimpleXMLElement $xmlElement) {
        $this->name = get_class($this);
        $this->xmlElement = $xmlElement;
        // atribui o conteúdo do label
        $attributes = $xmlElement->attributes();

        foreach ($attributes as $att => $value) {
            $this->properties[(string) $att] = (string) $value;
            // $this->$att = $value;
        }
        foreach ($xmlElement as $obj => $value) {
            $obj = ($obj == 'break') ? 'Breaker' : $obj;

            $className = CReport_Jasper_ElementFactory::getClassName($obj);
            if ($className == null) {
                if (!CReport_Jasper_ElementFactory::isIgnore($obj)) {
                    throw new Exception('Element ' . $obj . ' unknown');
                }
            }
            if ($className) {
                $this->add(new $className($value));
            }
        }
    }

    public function getProperty($key, $default = null) {
        return carr::get($this->properties, $key, $default);
    }

    /**
     * @param $child = objeto filho
     */
    public function add($child) {
        $this->children[] = $child;
    }

    public function getFirstValue($value) {
        return substr($value, 0, 1);
    }

    public function getChildsByClassName($childClassName) {
        $childs = [];
        foreach ($this->children as $child) {
            if (get_class($child) == 'CReport_Jasper_Element_' . $childClassName) {
                $childs[] = $child;
            }
        }

        return $childs;
    }

    public function getChildByClassName($childClassName) {
        foreach ($this->children as $child) {
            if (get_class($child) == 'CReport_Jasper_Element_' . $childClassName) {
                return $child;
            }
        }
    }

    public function recommendFont($utfstring, $defaultfont, $pdffont = '') {
        if ($pdffont != '') {
            return $pdffont;
        }
        if (preg_match("/\p{Han}+/u", $utfstring)) {
            $font = 'cid0cs';
        } elseif (preg_match("/\p{Katakana}+/u", $utfstring) || preg_match("/\p{Hiragana}+/u", $utfstring)) {
            $font = 'cid0jp';
        } elseif (preg_match("/\p{Hangul}+/u", $utfstring)) {
            $font = 'cid0kr';
        } else {
            $font = $defaultfont;
        }
        //echo "$utfstring $font".mb_detect_encoding($utfstring)."<br/>";

        return $font;//mb_detect_encoding($utfstring);
    }

    /**
     * @param null|mixed $obj
     */
    public function generate(CReport_Jasper_Report $report) {
        if ($this->children) {
            foreach ($this->children as $child) {
                if (is_object($child)) {
                    $child->generate($report);
                }
            }
        }
    }

    /**
     * @return CReport_Jasper_Report_Generator
     */
    public function getGenerator() {
        return CReport_Jasper_Manager::instance()->getGenerator();
    }
}
