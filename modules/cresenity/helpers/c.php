<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Common helper class.
 */
class c {

	public static function manimgurl($path) {
		return curl::base()."public/manual/".$path;
	}

	public static function get_empty( & $var, $default = NULL ){
		if ( isset( $var )&&!empty($var)) {
			return $var;
		} else {
			return $default;
		}
	}
	public static function get_null( & $var, $default = NULL ){
		if ( isset( $var )&&($var==null)) {
			return $var;
		} else {
			return $default;
		}
	}
	
	public static function get( & $var, $default = NULL ){
		if ( isset( $var ) ) {
			return $var;
		} else {
			return $default;
		}
	}
	
	public static function maybe_serialize( $data ) {
		if ( is_array( $data ) || is_object( $data ) ) {
			return serialize( $data );
		}

		return $data;
	}
	
	
	public static function htmlentities( $string, $preserve_encoded_entities = FALSE ) {
		if ( $preserve_encoded_entities ) {
			$translation_table = get_html_translation_table( HTML_ENTITIES, ENT_QUOTES, mb_internal_encoding() );
			$translation_table[chr(38)] = '&';
			return preg_replace( '/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/', '&amp;', strtr( $string, $translation_table ) );
		} else {
			return htmlentities( $string, ENT_QUOTES, mb_internal_encoding() );
		}
	}
} // End valid