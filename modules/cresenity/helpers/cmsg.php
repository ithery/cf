<?php defined('SYSPATH') OR die('No direct access allowed.');
class cmsg {
	public static function add($type,$message) {
		$session = Session::instance();
		$msgs = $session->get('cmsg_'.$type);
		if(!is_array($msgs)) {
			$msgs = array();
		}
		$msgs[] = $message;
		$session->set('cmsg_'.$type,$msgs);

	}
	public static function get($type) {
		$session = Session::instance();
		return $session->get('cmsg_'.$type);
	}
	public static function clear($type) {
		$session = Session::instance();
		$session->set('cmsg_'.$type,null);
	}
	public static function flash($type) {
		$msgs = cmsg::get($type);
		$message = "";
		if (is_array($msgs)) {
			foreach($msgs as $msg) {
				$message.= "<p>".$msg."</p>";
			}
		} else if (is_string($msgs)){
			if(strlen($msgs)>0) $message = $msgs;
		}
		cmsg::clear($type);
		if(strlen($message)>0) {
			$message = CMessage::factory()->set_type($type)->set_message($message)->html();
		}
		return $message;
	}
	public static function flash_all() {
		return cmsg::flash("error").cmsg::flash("warning").cmsg::flash("info").cmsg::flash("success");
	}	
	
}