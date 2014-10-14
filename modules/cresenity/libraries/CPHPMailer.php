<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * CPHPMailer. Helper class to make mail creation easier.
 *
 * @package    CLibrary
 * @author     Hery Kurniawan
 * @website    http://www.cresenitytech.com/
 * @license    NA
 */
require_once dirname(__FILE__)."/Lib/PHPMailer/class.phpmailer.php";
require_once dirname(__FILE__)."/Lib/PHPMailer/class.smtp.php";
require_once dirname(__FILE__)."/Lib/PHPMailer/class.pop3.php";

class CPHPMailer {
	private $phpmailer;

	
	public function __construct() {
		
		
		$this->phpmailer = new PHPMailer();
		
		
	}
	public static function factory($headers=array()) {
		$s = new CPHPMailer();
		return $s;
	}
	
	
	
}