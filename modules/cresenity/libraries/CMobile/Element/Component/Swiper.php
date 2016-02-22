<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Swiper extends CMobile_Element_AbstractComponent {

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Swiper($id);
    }

    public function add_item($id="") {
		$element = CMobile_Element_Component_Swiper_Item::factory($id);
		$this->add($element);
		return $element;
	}

	// public function build() {
	// 	$this->add('<div class="swiper-scrollbar"></div>');
		
	// }

	public function html($indent=0) {
		$this->add_class('swiper-wrapper');
		$html = new CStringBuilder();
        $html->set_indent($indent);
        $html->appendln('<div class="swiper-container">');
        $html->appendln(parent::html() . '');
        $html->appendln('<div class="swiper-pagination"></div>');
        $html->appendln('</div>');
        return $html->text();
	}

	public function js($indent=0) {
		
		$js = "
        var swiper = new Swiper('.swiper-container', {
	        pagination: '.swiper-pagination',
	        effect:'slide',
	        paginationHide:false,
	        scrollbarHide:true,
	        slidesPerView: 'auto',
	        paginationClickable: false,
	        lazyLoading:true,
	        spaceBetween: 1,
    	});
			
		";
		return $js;
	}
}
