<?php
class CView_Component_IconComponent extends CView_ComponentAbstract {
    /**
     * @var null|string
     */
    public $class;

    /**
     * @var string
     */
    public $width;

    /**
     * @var string
     */
    public $height;

    /**
     * @var string
     */
    public $role;

    /**
     * @var string
     */
    public $fill;

    /**
     * @var string
     */
    public $id;

    /**
     * Icon tag.
     *
     * @var string
     */
    private $path;

    /**
     * Create a new component instance.
     *
     * @param string      $path
     * @param null|string $id
     * @param null|string $class
     * @param string      $width
     * @param string      $height
     * @param string      $role
     * @param string      $fill
     */
    public function __construct(
        string $path,
        string $id = null,
        string $class = null,
        string $width = '1em',
        string $height = '1em',
        string $role = 'img',
        string $fill = 'currentColor'
    ) {
        $this->path = $path;
        $this->id = $id;
        $this->class = $class;
        $this->width = $width;
        $this->height = $height;
        $this->role = $role;
        $this->fill = $fill;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return CManager_Icon_IconHtml
     */
    public function render() {
        $icon = c::manager()->icon()->loadFile($this->path);

        $content = $this->setAttributes($icon);

        return new CManager_Icon_IconHtml($content);
    }

    /**
     * @param null|string $icon
     *
     * @return string
     */
    private function setAttributes($icon) {
        if ($icon === null) {
            return '';
        }

        $dom = new DOMDocument();
        $dom->loadXML($icon);

        /** @var \DOMElement $item */
        $item = c::collect($dom->getElementsByTagName('svg'))->first();

        c::collect($this->data())
            ->except('attributes')
            ->filter(function ($value) {
                return $value !== null && is_string($value);
            })
            ->each(function ($value, $key) use ($item) {
                $item->setAttribute($key, $value);
            });

        return $dom->saveHTML();
    }
}
