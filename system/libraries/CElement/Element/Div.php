<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Element_Div extends CElement_Element {
    use CTrait_Element_Handler_ReloadHandler;
    use CTrait_Element_Property_DependsOn;

    /**
     * @param null|string $id
     *
     * @return void
     */
    public function __construct($id = null) {
        parent::__construct($id);
        $this->tag = 'div';
    }

    /**
     * @param null|string $id
     *
     * @return static
     */
    public static function factory($id = null) {
        // @phpstan-ignore-next-line
        return new static($id);
    }

    /**
     * @return void
     */
    protected function build() {
        parent::build();
        $this->bootBuildReloadHandler();
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $js = new CStringBuilder();

        $js->append(parent::js());
        $js->append($this->getDependsOnContentJavascript());

        return $js->text();
    }
}
