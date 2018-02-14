<?php

class CResources_Engine_Image extends CResources_Engine {

    public function __construct($type, $org_code) {
        parent::__construct('Image', $type, $org_code);
    }

    public function add_size($size_name, $options) {
        $this->_sizes[$size_name] = $options;
    }

    public function resizeAndSave($fullfilename, $sizeName, $sizeOptions) {
        if(strlen($fullfilename)==0) {
            return null;
        }
        $filename = basename($fullfilename);
        $path = dirname($fullfilename) . DS;
        $width = carr::get($sizeOptions, 'width', '100');
        $height = carr::get($sizeOptions, 'height', '100');
        $crop = carr::get($sizeOptions, 'crop', true);
        $proportional = carr::get($sizeOptions, 'proportional', true);
        $whitespace = carr::get($sizeOptions, 'whitespace', true);

        $new_width = $width;
        $new_height = $height;
        list($img_width, $img_height) = @getimagesize($fullfilename);

        if ($proportional) {
            if (!$img_width || !$img_height) {
                throw new Exception('Fail to getimagesize ' . $fullfilename);
            }
            $scale = min($width / $img_width, $height / $img_height);

            if ($scale >= 1) {

                ///////////////////// jja ///////////////////// 
                $size_path = $path . $sizeName . DS;
                if (!is_dir($size_path)) {
                    mkdir($size_path);
                }
                $file_path = $fullfilename;
                $new_file_path = $size_path . $filename;
                ///////////////////// jja ///////////////////// 


                if ($file_path !== $new_file_path) {

                    copy($file_path, $new_file_path);
                }
            }
            $new_width = $img_width * $scale;
            $new_height = $img_height * $scale;
        }
        $size_path = $path . $sizeName . DS;
        if (!is_dir($size_path)) {
            @mkdir($size_path);
        }
        $full_size_path = $size_path . $filename;

        try {
            //resize to propotional to maximum size, reduce memory load
            $maxScale = 1;
            if ($img_width > 0 && $img_height > 0) {
                $maxScale = max($width / $img_width, $height / $img_height);
            }
            $maxPropWidth = $img_width * $maxScale;
            $maxPropHeight = $img_height * $maxScale;

            $ext = pathinfo($fullfilename, PATHINFO_EXTENSION);
            if ($ext == 'png') {
                $src = @imagecreatefrompng($fullfilename);
            } else {
                $src = @imagecreatefromjpeg($fullfilename);
            }
            $dst = @imagecreatetruecolor($maxPropWidth, $maxPropHeight);
            @imagecopyresampled($dst, $src, 0, 0, 0, 0, $maxPropWidth, $maxPropHeight, $img_width, $img_height);
            unset($src);
            if($dst==null) {
                throw new Exception('Error when resizing image '.$fullfilename);
            }
            $wideimage = CWideImage::load($dst);

            if ($crop) {
                $wideimage = $wideimage->crop('center', 'center', $width, $height);
            }


            if ($whitespace) {
                $w = ($maxPropHeight / $height) * $width;
                $h = ($maxPropWidth / $width) * $height;
                $height_new = $maxPropHeight;
                $width_new = $maxPropWidth;
                if ($w > $h) {
                    $width_new = $w;
                } else {
                    $height_new = $h;
                }

                $white = $wideimage->allocateColor(255, 255, 255);

                //throw new Exception("Width:".$width.", Height:".$height.", Img Width:".$img_width.", Img Height:".$img_height.", Width New:".$width_new.", Height New:".$height_new);
                $wideimage = $wideimage->resizeCanvas($width_new, $height_new, 'center', 'center', $white);
            }
            $wideimage = $wideimage->resize($new_width, $new_height);
            if ($crop) {
                $wideimage = $wideimage->crop('center', 'center', $width, $height);
            }


            $wideimage->saveToFile($full_size_path);
        } catch (CResources_Exception $ex) {
            throw $ex;
        }
        return $full_size_path;
    }

    public function save($file_name, $file_request) {
        $filename = parent::save($file_name, $file_request);
        $fullfilename = parent::get_path($filename);
        $path = dirname($fullfilename) . DS;
        foreach ($this->_sizes as $k => $size) {
            $width = carr::get($size, 'width', '100');
            $height = carr::get($size, 'height', '100');
            $crop = carr::get($size, 'crop', true);
            $proportional = carr::get($size, 'proportional', true);
            $whitespace = carr::get($size, 'whitespace', true);

            $new_width = $width;
            $new_height = $height;
            list($img_width, $img_height) = @getimagesize($fullfilename);
            if ($proportional) {

                if (!$img_width || !$img_height) {
                    throw new Exception('Fail to getimagesize ' . $fullfilename);
                }
                $scale = min($width / $img_width, $height / $img_height);
                if ($scale >= 1) {

                    ///////////////////// jja ///////////////////// 
                    $size_path = $path . $k . DS;
                    if (!is_dir($size_path)) {
                        mkdir($size_path);
                    }
                    $file_path = $fullfilename;
                    $new_file_path = $size_path . $filename;
                    ///////////////////// jja ///////////////////// 

                    if ($file_path !== $new_file_path) {

                        copy($file_path, $new_file_path);
                    }
                }
                $new_width = $img_width * $scale;
                $new_height = $img_height * $scale;
            }
            $size_path = $path . $k . DS;
            if (!is_dir($size_path)) {
                mkdir($size_path);
            }
            $full_size_path = $size_path . $filename;

            try {
                $wideimage = CWideImage::load($fullfilename);
                if ($whitespace) {
                    $w = ($img_height / $height) * $width;
                    $h = ($img_width / $width) * $height;
                    $height_new = $img_height;
                    $width_new = $img_width;
                    if ($w > $h) {
                        $width_new = $w;
                    } else {
                        $height_new = $h;
                    }
                    $white = $wideimage->allocateColor(255, 255, 255);
                    //throw new Exception("Width:".$width.", Height:".$height.", Img Width:".$img_width.", Img Height:".$img_height.", Width New:".$width_new.", Height New:".$height_new);
                    $wideimage = $wideimage->resizeCanvas($width_new, $height_new, 'center', 'center', $white);
                }
                $wideimage = $wideimage->resize($new_width, $new_height);
                if ($crop) {
                    $wideimage = $wideimage->crop('center', 'center', $width, $height);
                }


                $wideimage->saveToFile($full_size_path);
            } catch (CResources_Exception $ex) {
                throw $ex;
            }
        }

        return $filename;
    }

}
