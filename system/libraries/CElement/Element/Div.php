<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Oct 28, 2017, 2:25:21 PM
 */
class CElement_Element_Div extends CElement_Element {
    use CTrait_Element_Handler_ReloadHandler;
    use CTrait_Element_Property_DependsOn;

    public function __construct($id = null) {
        parent::__construct($id);
        $this->tag = 'div';
    }

    public static function factory($id = null) {
        // @phpstan-ignore-next-line
        return new static($id);
    }

    protected function build() {
        parent::build();
        $this->bootBuildReloadHandler();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();

        $js->append(parent::js());
        $js->append($this->getDependsOnContentJavascript());

        return $js->text();
    }
}
