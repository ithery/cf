<?php

class CTreeNode {
	private $value;
	private $parent;
	private $children;
	private $id;
	private function __construct($value=null,$children = array()) {
		$this->value = $value;
		
		$this->children = $children;
	}
	public static function factory($value=null,$children=array()) {
		return new CTreeNode($value,$children);
	}
	/**
     * Set the value of the current node
     *
     * @param mixed $value
     *
     * @return NodeInterface the current instance
     */
	public function set_value($val) {
		$this->value=$val;
		return $this;
	}
	public function set_id($id) {
		$this->id=$id;
		return $this;
	}
    /**
     * Get the current node value
     *
     * @return mixed
     */	
	public function get_value() {
		return $this->value;
	}
	public function get_id() {
		return $this->id;
	}
	 /**
     * Add a child
     *
     * @param CTreeNode $child
     *
     * @return mixed
     */
	public function add_child($child) {
		
		if(!CTreeNode::is_instanceof($child)) {
			$child = CTreeNode::factory($child);
		}
		
		$child->set_parent($this);
		$this->children[] = $child;
		
		return $child;
	}
	
	public function index() {
		if($this->is_root()) return 0;
		$parent = $this->get_parent();
		$i=0;
		foreach($parent->get_children() as $child) {
			if($this==$child) {
				return $i;
			}
			$i++;
		}
		return null;
	}
	
	public function remove_child($child) {
		if(!CTreeNode::is_instanceof($child)) {
			$child = CTreeNode::factory($child);
		}
		foreach ($this->children as $key => $myChild) {
            if ($child == $myChild) {
                unset($this->children[$key]);
            }
        }
		$this->children = array_values($this->children);
		$child->set_parent(null);
		return $this;
	}
	
	public function remove_all_children() {
		foreach($this->children as $child) {
			$child->remove_all_children();
			$child->set_parent($null);
		}
		$this->set_children(array());
		return $this;
	}
	
	public function get_children() {
		return $this->children;
	}
	
	public function set_children(array $children) {
        $this->remove_parent_from_children();
        $this->children = array();

        foreach ($children as $child) {
            $this->add_child($child);
        }

        return $this;
    }
	
	public function set_parent($parent = null) {
        if($parent!=null) {
			if(!CTreeNode::is_instanceof($parent)) {
				$parent = CTreeNode::factory($parent);
			}
		}
		$this->parent = $parent;
		
    }
	public function get_parent() {
		return $this->parent;
	}
	
	public function get_ancestors() {
		$parents = array();
		$node = $this;
		while($parent = $node->get_parent()) {
			array_unshift($parents,$parent);
			$node = $parent;
			
		}
		return $parents;
	}
	
	public function get_level() {
		return count($this->get_ancestors());
	}
	public function get_ancestors_and_self() {
        return array_merge($this->get_ancestors(), array($this));
    }
	
	public function get_siblings() {
        $siblings = $this->get_parent()->get_children();
        $current = $this;
		$ret = array();
		foreach($siblings as $key=>$s) {
			 if ($s != $current) {
                $ret[] = $s;
            }
		}
		return $ret;
		
    }
	
	public function get_siblings_and_self() {
        return $this->get_parent()->get_children();
    }
	public function is_leaf() {
		return count($this->children)===0;
	}
	public function is_child() {
        return $this->get_parent() !== null;
    }
	public function is_root() {
		return $this->get_parent()===null;
	}
	
	public function root() {
		$node = $this;
		while ($parent = $node->get_parent()) $node = $parent;

        return $node;
	}
	
	public static function is_instanceof($value) {
        if (is_object($value)) {
            return ($value instanceof CTreeNode);
        }
        return false;
    }
	
	private function remove_parent_from_children() {
        foreach ($this->get_children() as $child)
            $child->set_parent(null);
    }
}