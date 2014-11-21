<?php

defined('SYSPATH') OR die('No direct access allowed.');

class cimage {

    public static function create_scaled_image($file_path, $options) {
        $file_name = basename(stripslashes($file_path));
        $new_file_path = $options['upload_dir'] . $file_name;
        list($img_width, $img_height) = @getimagesize($file_path);
        if (!$img_width || !$img_height) {
            return false;
        }
        $scale = max(
                $options['max_width'] / $img_width, $options['max_height'] / $img_height
        );
        if ($scale >= 1) {
            if ($file_path !== $new_file_path) {
                return copy($file_path, $new_file_path);
            }
            return true;
        }
        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        try {
            CWideImage::load($file_path)->resize($new_width, $new_height)->crop('center', 'center', $options['max_width'], $options['max_height'])->saveToFile($new_file_path);
        } catch (Exception $ex) {
            return false;
        }
        return true;
    }

    public static function create_image_folder($type, $id) {
        $org = CApp::instance()->org();
        $org_folder = "";
        if ($org != null) {
            $org_folder = $org->code . DIRECTORY_SEPARATOR;
        }
		$upload_directory = DOCROOT.'upload'.DIRECTORY_SEPARATOR;
		ctemp::makedir($upload_directory);
		$org_directory = $upload_directory . $org_folder;
		ctemp::makedir($org_directory);
		$type = explode(".",$type);
		$type_directory = $org_directory;
		foreach($type as $t) {
			if(strlen(trim($t))>0) {
				$type_directory =  $type_directory.$t.DIRECTORY_SEPARATOR;
				ctemp::makedir($type_directory);
			}
		}
        
        

		
        $id_directory = $type_directory . $id . DIRECTORY_SEPARATOR;
        ctemp::makedir($id_directory);
        $original_directory = $id_directory . 'original' . DIRECTORY_SEPARATOR;
        ctemp::makedir($original_directory);
		$wide_directory = $id_directory . 'wide' . DIRECTORY_SEPARATOR;
		ctemp::makedir($wide_directory);
        $large_directory = $id_directory . 'large' . DIRECTORY_SEPARATOR;
		ctemp::makedir($large_directory);
        $medium_directory = $id_directory . 'medium' . DIRECTORY_SEPARATOR;
		ctemp::makedir($medium_directory);
        $small_directory = $id_directory . 'small' . DIRECTORY_SEPARATOR;
		ctemp::makedir($small_directory);
        $thumbnail_directory = $id_directory . 'thumbnail' . DIRECTORY_SEPARATOR;
		ctemp::makedir($thumbnail_directory);

        
        ctemp::makedir($org_directory);
        ctemp::makedir($type_directory);
        ctemp::makedir($id_directory);
        
       
        
        
        
        
    }

    public static function delete_all_image($type, $id, $filename) {
        $file = cimage::get_upload_path($type, $id, "original") . $filename;
        if (is_file($file))
            unlink($file);
        $file = cimage::get_upload_path($type, $id, "wide") . $filename;
        if (is_file($file))
            unlink($file);
        $file = cimage::get_upload_path($type, $id, "large") . $filename;
        if (is_file($file))
            unlink($file);
        $file = cimage::get_upload_path($type, $id, "medium") . $filename;
        if (is_file($file))
            unlink($file);
        $file = cimage::get_upload_path($type, $id, "small") . $filename;
        if (is_file($file))
            unlink($file);
        $file = cimage::get_upload_path($type, $id, "thumbnail") . $filename;
        if (is_file($file))
            unlink($file);
    }

    public static function get_upload_path($type, $id, $size) {
        $org = CApp::instance()->org();
        $upload_directory = DOCROOT . 'upload' . DIRECTORY_SEPARATOR;
        $org_folder = "";
        if ($org != null) {
            $org_folder = $org->code . DIRECTORY_SEPARATOR;
        }
        $org_directory = $upload_directory . $org_folder;
		
		$type = explode(".",$type);
		$type_directory = $org_directory;
		foreach($type as $t) {
			if(strlen(trim($t))>0) {
				$type_directory =  $type_directory.$t.DIRECTORY_SEPARATOR;
			}
		}
        
        $id_directory = $type_directory . $id . DIRECTORY_SEPARATOR;
        $size_directory = $id_directory . $size . DIRECTORY_SEPARATOR;
        return $size_directory;
    }

    public static function resize_image($type, $id, $filename) {
        $versions = array(
            // Uncomment the following version to restrict the size of
            // uploaded images. You can also add additional versions with
            // their own upload directories:

            'wide' => array(
                'upload_dir' => cimage::get_upload_path($type, $id, "wide"),
                'upload_url' => cimage::get_upload_url($type, $id, "wide", $filename),
                'max_width' => 973,
                'max_height' => 352,
                'jpeg_quality' => 95
            ),
            'large' => array(
                'upload_dir' => cimage::get_upload_path($type, $id, "large"),
                'upload_url' => cimage::get_upload_url($type, $id, "large", $filename),
                'max_width' => 1280,
                'max_height' => 768,
                'jpeg_quality' => 95
            ),
            'medium' => array(
                'upload_dir' => cimage::get_upload_path($type, $id, "medium"),
                'upload_url' => cimage::get_upload_url($type, $id, "medium", $filename),
                'max_width' => 600,
                'max_height' => 400,
                'jpeg_quality' => 95
            ),
            'small' => array(
                'upload_dir' => cimage::get_upload_path($type, $id, "small"),
                'upload_url' => cimage::get_upload_url($type, $id, "small", $filename),
                'max_width' => 120,
                'max_height' => 120,
                'jpeg_quality' => 95
            ),
            'thumbnail' => array(
                'upload_dir' => cimage::get_upload_path($type, $id, "thumbnail"),
                'upload_url' => cimage::get_upload_url($type, $id, "thumbnail", $filename),
                'max_width' => 40,
                'max_height' => 40,
                'jpeg_quality' => 95,
            )
        );
        foreach ($versions as $version => $options) {
            cimage::create_scaled_image($filename, $options);
        }
    }

    public static function get_image_src($type, $id, $size, $filename) {
        
		$upload_directory = curl::base().'upload'.'/';
		
		$org = CApp::instance()->org();
        $org_path = "";
        if ($org != null) {
            $org_path = $org->code . "/";
        }
		$org_directory = $upload_directory.$org_path;
		
		$type = explode(".",$type);
		$type_directory = $org_directory;
		foreach($type as $t) {
			if(strlen(trim($t))>0) {
				$type_directory =  $type_directory.$t.DIRECTORY_SEPARATOR;
			}
		}
		$id_directory =  $type_directory.$id.'/';
		$size_directory =  $id_directory.$size.'/';
		return $size_directory.$filename;

    }

    public static function get_upload_url($type, $id, $size, $filename) {
        return cimage::get_image_src($type, $id, $size, $filename);
    }

    public static function get_image_path($type, $id, $size, $filename = "") {

        return cimage::get_upload_path($type, $id, $size) . DIRECTORY_SEPARATOR . $filename;
    }

}