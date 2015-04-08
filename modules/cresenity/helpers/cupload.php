<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Upload helper class for working with the global $_FILES
 * array and Validation library.
 *
 * $Id: upload.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class cupload {

	/**
	 * Save an uploaded file to a new location.
	 *
	 * @param   mixed    name of $_FILE input or array of upload data
	 * @param   string   new filename
	 * @param   string   new directory
	 * @param   integer  chmod mask
	 * @return  string   full path to new file
	 */
	public static function save($file, $filename = NULL, $directory = NULL, $chmod = 0644)
	{
		// Load file data from FILES if not passed as array
		$file = is_array($file) ? $file : $_FILES[$file];

		if ($filename === NULL)
		{
			// Use the default filename, with a timestamp pre-pended
			$filename = time().$file['name'];
		}

		if (CF::config('upload.remove_spaces') === TRUE)
		{
			// Remove spaces from the filename
			$filename = preg_replace('/\s+/', '_', $filename);
		}

		if ($directory === NULL)
		{
			// Use the pre-configured upload directory
			$directory = CF::config('upload.directory', TRUE);
		}

		// Make sure the directory ends with a slash
		$directory = rtrim($directory, '/').'/';

		if ( ! is_dir($directory) AND CF::config('upload.create_directories') === TRUE)
		{
			// Create the upload directory
			mkdir($directory, 0777, TRUE);
		}

		if ( ! is_writable($directory))
			// throw new Kohana_Exception('upload.not_writable', $directory);
			throw new Exception('upload.not_writable', $directory);

		if (is_uploaded_file($file['tmp_name']) AND move_uploaded_file($file['tmp_name'], $filename = $directory.$filename))
		{
			if ($chmod !== FALSE)
			{
				// Set permissions on filename
				chmod($filename, $chmod);
			}

			// Return new file path
			return $filename;
		}

		return FALSE;
	}

	/* Validation Rules */

	/**
	 * Tests if input data is valid file type, even if no upload is present.
	 *
	 * @param   array  $_FILES item
	 * @return  bool
	 */
	public static function valid($file)
	{
		return (is_array($file)
			AND isset($file['error'])
			AND isset($file['name'])
			AND isset($file['type'])
			AND isset($file['tmp_name'])
			AND isset($file['size']));
	}

	/**
	 * Tests if input data has valid upload data.
	 *
	 * @param   array    $_FILES item
	 * @return  bool
	 */
	public static function required(array $file)
	{
		return (isset($file['tmp_name'])
			AND isset($file['error'])
			AND is_uploaded_file($file['tmp_name'])
			AND (int) $file['error'] === UPLOAD_ERR_OK);
	}

	/**
	 * Validation rule to test if an uploaded file is allowed by extension.
	 *
	 * @param   array    $_FILES item
	 * @param   array    allowed file extensions
	 * @return  bool
	 */
	public static function type(array $file, array $allowed_types)
	{
		if ((int) $file['error'] !== UPLOAD_ERR_OK)
			return TRUE;

		// Get the default extension of the file
		$extension = strtolower(substr(strrchr($file['name'], '.'), 1));

		// Get the mime types for the extension
		$mime_types = CF::config('mimes.'.$extension);

		// Make sure there is an extension, that the extension is allowed, and that mime types exist
		return ( ! empty($extension) AND in_array($extension, $allowed_types) AND is_array($mime_types));
	}

	/**
	 * Validation rule to test if an uploaded file is allowed by file size.
	 * File sizes are defined as: SB, where S is the size (1, 15, 300, etc) and
	 * B is the byte modifier: (B)ytes, (K)ilobytes, (M)egabytes, (G)igabytes.
	 * Eg: to limit the size to 1MB or less, you would use "1M".
	 *
	 * @param   array    $_FILES item
	 * @param   array    maximum file size
	 * @return  bool
	 */
	public static function size(array $file, array $size)
	{
		if ((int) $file['error'] !== UPLOAD_ERR_OK)
			return TRUE;

		// Only one size is allowed
		$size = strtoupper($size[0]);

		if ( ! preg_match('/[0-9]++[BKMG]/', $size))
			return FALSE;

		// Make the size into a power of 1024
		switch (substr($size, -1))
		{
			case 'G': $size = intval($size) * pow(1024, 3); break;
			case 'M': $size = intval($size) * pow(1024, 2); break;
			case 'K': $size = intval($size) * pow(1024, 1); break;
			default:  $size = intval($size);                break;
		}

		// Test that the file is under or equal to the max size
		return ($file['size'] <= $size);
	}
	/*upload logic*/
	public static function create_upload_folder($type,$id) {
		
		
		$upload_directory = DOCROOT.'upload'."/";
		ctemp::makedir($upload_directory);
		$type = explode(".",$type);
		$type_directory = $upload_directory;
		foreach($type as $t) {
			if(strlen(trim($t))>0) {
				$type_directory =  $type_directory.$t."/";
				ctemp::makedir($type_directory);
			}
		}
		
		
		$id_directory =  $type_directory.$id."/";
		
		

		
		
		ctemp::makedir($id_directory);
	
	}
	
	public static function delete_all_file($type,$id,$filename) {
		$file = cupload::get_upload_path($type,$id).$filename;
		if (is_file($file)) unlink($file);
		
	}
	public static function get_upload_path($type,$id) {
		$upload_directory = DOCROOT.'upload'."/";
		
		$type = explode(".",$type);
		$type_directory = $upload_directory;
		foreach($type as $t) {
			if(strlen(trim($t))>0) {
				$type_directory =  $type_directory.$t."/";
			}
		}
		$id_directory =  $type_directory.$id."/";
		return $id_directory;
	}
	public static function get_upload_src($type,$id,$filename) {
		$upload_directory = curl::base().'upload'.'/';
		$type = explode(".",$type);
		$type_directory = $upload_directory;
		foreach($type as $t) {
			if(strlen(trim($t))>0) {
				$type_directory =  $type_directory.$t."/";
			}
		}
		$id_directory =  $type_directory.$id.'/';
		return $id_directory.$filename;
	}
} // End upload