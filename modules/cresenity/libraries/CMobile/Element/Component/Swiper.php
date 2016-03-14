<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Swiper extends CMobile_Element_AbstractComponent {

	protected $wrapper;
    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
		$this->wrapper = $this->add_div($this->id."_wrapper")->add_class('swiper-wrapper');
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Swiper($id);
    }

    public function add_item($id="") {
		$element = CMobile_Element_Component_Swiper_Item::factory($id);
		$this->wrapper->add($element);
		return $element;
	}


	public function build() {
		$this->add_class('swiper-container');
		$this->add_div($this->id."_pagination")->add_class('swiper-pagination');
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
		$js.=parent::js();
		return $js;
	}
}
