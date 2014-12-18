<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class CHandler_Driver {

	protected $url;
	protected $name;
	protected $event;
	protected $owner;
	protected $url_param;
	
	protected function __construct($owner,$event,$name) {
		$this->name = $name;
		$this->event = $event;
		$this->owner= $owner;
		$this->url = "";
		$this->url_param = array();
		
	}
	
	public function set_url($url) {
		$this->url = $url;
		return $this;
	}
	
	public function set_owner($owner) {
		$this->owner = $owner;
		return $this;
	}
	
	
	
	public function set_url_param($url_param) {
		if(!is_array($url_param)) {
			trigger_error('Invalid URL Param '.cdbg::var_dump($url_param,true).'');
		}
		$this->url_param=$url_param;
		return $this;
	}
	
	public function add_url_param($k,$url_param) {
		$this->url_param[$k]=$url_param;
		return $this;
	}
	
	public function generated_url() {
		$link = $this->url;
		
		if(strlen($link)==0) {
			$ajax_url = CAjaxMethod::factory()->set_type('handler_'.$this->name)
			->set_data('json', $this->content->json())
			->makeurl();
			$link = $ajax_url;
		}
		
		foreach($this->url_param as $k=>$p) {
			preg_match_all("/{([\w]*)}/", $link, $matches, PREG_SET_ORDER);
			foreach ($matches as $val) {
				$str = $val[1]; //matches str without bracket {}
				$b_str = $val[0]; //matches str with bracket {}
				if($k==$str) {
					$link = str_replace($b_str,$p,$link);
				}
			}
		}
		return $link;
	}
	
	protected function script() {
		if(strlen($this->target)==0) {
			//$this->target = crequest::current_container_id();
		}
		$js = "";
		return $js;
		
	}
	
	
} // End CHandler_Driver
