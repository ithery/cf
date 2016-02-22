<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Carousel extends CMobile_Element_AbstractComponent {

	protected $img;
	protected $slide;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
		$this->img = array();
		$this->slide = false;
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Carousel($id);
    }
	
    public function set_slide($slide) {
		$this->slide = $slide;
		return $this;
	}
	
	public function add_image($img) {
		if(!is_array($img)) {
			$img = array($img);
		}
		foreach($img as $i) {
			$this->img[] = $i;
		}
		
		
		return $this;
	}
	public function add_item($id="") {
		$element = CMobile_Element_Component_Carousel_Item::factory($id);
		$this->add($element);
		return $element;
	}
	
	public function build() {
		// <div class="carousel">
			// <a class="carousel-item" href="#one!"><img src="http://lorempixel.com/250/250/nature/1"></a>
			// <a class="carousel-item" href="#two!"><img src="http://lorempixel.com/250/250/nature/2"></a>
			// <a class="carousel-item" href="#three!"><img src="http://lorempixel.com/250/250/nature/3"></a>
			// <a class="carousel-item" href="#four!"><img src="http://lorempixel.com/250/250/nature/4"></a>
			// <a class="carousel-item" href="#five!"><img src="http://lorempixel.com/250/250/nature/5"></a>
		  // </div>
		$this->add_class('carousel');
		if($this->slide) {
			$this->add_class('carousel-slider');
		}
		
		foreach($this->img as $v) {
			
			$this->add('<a class="carousel-item" href="javascript:;"><img src="'.$v.'"></a>');
		}
	}
	
	public function js($indent=0) {
		if($this->slide) {
			
			$js = "
				$('#".$this->id."').carousel({padding:0,full_width: true});
				
			";
		} else {
			$js = "
				$('#".$this->id."').carousel();
				
			";
			
		}
		$js .= "
		(function a() {
			setTimeout(function() {
				$('#".$this->id."').carousel('next');
				a();
	        }, 5000);
			
		})();
			
		";
		return $js;
	}


   

}
