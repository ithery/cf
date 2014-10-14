<?php

class CTree {
	private $root;
	private $size;
	private $nodes;
	private $walker_callback;
	private function __construct($id,$node=null) {
		
		if(!CTreeNode::is_instanceof($node)) {
			$root = CTreeNode::factory($node);
		}
		$this->root = $root;
		$this->size = 1;
		$root->set_id($id);
		$this->nodes=array($id=>$root);
		$this->walker_callback=null;
		$this->walker_callback_require = array();
		
	}
	
	
	public static function factory($id,$node=null) {
		return new CTree($id,$node);
	}
	
	public function set_walker_callback($callback,$walker_callback_require=null) {
		
		if(is_callable($callback)) {
			$this->walker_callback=$callback;
			
			$this->walker_callback_require=$walker_callback_require;
		}
	}
	
	public function root() {
		return $this->root;
	}
	
	public function get_node($id) {
		$ret = null;
		//look for the node in the hash table
		//return false if not found
        if(array_key_exists($id,$this->nodes) === true) {
            $ret = $this->nodes[$id];
        }
        return $ret;
	}
	
	public function add_child($id,$child_id,$value) {
		
		if(CTreeNode::is_instanceof($id)) {
			$id = $id->get_id();
		}
		if(!array_key_exists($id,$this->nodes) === true) {
			trigger_error('Node not exists for id '.$id);
		}
		if(array_key_exists($child_id,$this->nodes) === true) {
			trigger_error('Node exists for child_id '.$child_id);
		}
		if(!CTreeNode::is_instanceof($value)) {
			$value = CTreeNode::factory($value);
		}
		$value->set_id($child_id);
		$parent = $this->get_node($id);
		$parent->add_child($value);
		$this->nodes[$child_id]=$value;
	}
	
	public function move_child($child,$parent) {
		
		if(!CTreeNode::is_instanceof($child)) {
			$child = $this->get_node($child);
		}
		if(!CTreeNode::is_instanceof($parent)) {
			$parent = $this->get_node($parent);
		}
		$child->get_parent()->remove_child($child);
		$parent->add_child($child);
		
	}
	
	public function create_node($id,$value=null) {
        return CTreeNode::factory($id,$value);
	}
	
	public function walk($callback,$node=null) {
		if($node==null) {
			$node = $this->root;
		}
		call_user_func($callback,$node);
		foreach($node->get_children() as $c) {
			$this->walk($callback,$c);
		}
	}
	
	/*
		return array (
			array(
				"id"=>"1",
				"value"=>"1",
				"children"=>array(
					array(
						"id"=>"2",
						"value"=>"2",
						"children"=>array(),
					),
				),
				
			),
		);
	*/
	
	
	
	public function toarray($node=null,$child_key="children",$id_key="id",$value_key="value") {
		if($node==null) {
			$node = $this->root;
		}
		$data = array();
		$child_array=array();
		foreach($node->get_children() as $child) {
			$child_array[] = $this->toarray($child,$child_key,$id_key,$value_key);
		}
		
		
		
		$data = array(
			$id_key=>$node->get_id(),
			$value_key=>$node->get_value(),
			$child_key=>$child_array,
		);
		return $data;
	}
	
	public function dump($return=false) {
		$array = $this->toarray();
		return cdbg::var_dump($array,$return);
		
	}
	
	public function html($node=null,$options=array()) {
		$default_option = array(
			"indent_start"=>0,
			"indent"=>"\t",
			"eol"=>PHP_EOL,
			"ul_id"=>"tree-ul-{id}",
			"li_id"=>"tree-li-{id}-{n}",
			"ul_tag"=>"ul",
			"li_tag"=>"li",
			"ul_class"=>"ul-tree level-{level}",
			"li_class"=>"li-tree level-{level}",
			"callback"=>null,
		);
		$options = array_merge($default_option,$options);
		if($node==null) {
			$node = $this->root;
		}
		$level_indent = $node->get_level()+$options["indent_start"];
		$html_child = '';
		foreach($node->get_children() as $child) {
			
			
			$html_child .= $this->html($child,$options);
			
		}
		
		$value = $node->get_value();
		if(is_callable($this->walker_callback)) {
			if($this->walker_callback_require!=null) {
				require_once $this->walker_callback_require;
			}
			$value = call_user_func($this->walker_callback,$this,$node,$node->get_value());
		}
		if(is_array($value)) {
			$value = implode(",",$value);
		}
		$ul_id = $options["ul_id"];
		$li_id = $options["li_id"];
		
		$ul_class = $options["ul_class"];
		$li_class = $options["li_class"];
		
		if(strlen(trim($ul_class))>0) {
			$ul_class = ' class="'.$ul_class.'"';
		}
		if(strlen(trim($li_class))>0) {
			$li_class = ' class="'.$li_class.'"';
		}
		if(strlen(trim($ul_id))>0) {
			$ul_id = ' id="'.$ul_id.'"';
		}
		if(strlen(trim($li_id))>0) {
			$li_id = ' id="'.$li_id.'"';
		}
		$ul_class=str_replace('{level}',$node->get_level(),$ul_class);
		$li_class=str_replace('{level}',$node->get_level(),$li_class);
		
		$ul_id=str_replace('{level}',$node->get_level(),$ul_id);
		$ul_id=str_replace('{id}',$node->get_id(),$ul_id);
		$ul_id=str_replace('{n}',$node->index(),$ul_id);
		$li_id=str_replace('{level}',$node->get_level(),$li_id);
		$li_id=str_replace('{n}',$node->index(),$li_id);
		$li_id=str_replace('{id}',$node->get_id(),$li_id);
		
		$html = '';
		$html .= str_repeat($options['indent'],($level_indent)*2).'<'.$options['ul_tag'].$ul_id.$ul_class.'>'.$options["eol"];
		$html .= str_repeat($options['indent'],(($level_indent)*2)+1).'<'.$options['li_tag'].$li_id.$li_class.'>'.$value;
		
		if(strlen($html_child)>0) {
			$html .=$options["eol"].$html_child.str_repeat($options['indent'],(($level_indent)*2)+1);
		}
		$html .= '</'.$options['li_tag'].'>'.$options["eol"];
		
		$html .= str_repeat($options['indent'],$level_indent*2).'</'.$options['ul_tag'].'>'.$options["eol"];
		

		
		
		return $html;
	}
	
	
	
}