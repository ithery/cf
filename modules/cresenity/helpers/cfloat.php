<?php
class cfloat {
	public function mod($x,$i) {
		return ($x-floor($x/$i)*$i);
	}
	
}