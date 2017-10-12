<?php

class Res_controller extends CController {

    public function __construct() {
        parent::__construct();
    }

    public function show($param_size, $param_filename = null) {
        $this->image($param_size,$param_filename);
    }
    public function image($param_size, $param_filename = null) {
        $app = CApp::instance();
        $org = $app->org();
        $org_code = null;
        if ($org) {
            $org_code = $org->org_code;
        }
        $size = $param_size;
        $filename = $param_filename;
        if ($filename == null) {
            $filename = $size;
            $size = null;
        }
        $filename = CResources_Decode::decode($filename);
        $file_path = CResources::get_path($filename, $size);



        if (!cfs::file_exists($file_path)) {
            $file_path = DOCROOT . 'modules/cresenity/media/img/no-image.png';
        }



        $info = CResources::get_file_info($filename);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $cache_length = 2592000;
        $cache_expire_date = gmdate("D, d M Y H:i:s", time() + $cache_length) . ' GMT';
        $content_type = 'Content-Type:' . carr::get($info, 'resource_type') . '/' . $extension;

        $language = 'ID';

        $timestamp = @filemtime($file_path);

        $tsstring = gmdate('D, d M Y H:i:s ', $timestamp) . 'GMT';
        $etag = $language . $timestamp;
        $etag = md5($filename);



        $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
        $not_modified = false;
        if ($if_modified_since) {
            $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : false;
            $if_modified_since_time = strtotime($if_modified_since);
            $current_time = strtotime(date('Y-m-d H:i:s'));

            if (($current_time - $if_modified_since_time) < $cache_length) {

                $not_modified = true;
            }
        }


        if ($if_modified_since) {

            header('HTTP/1.1 304 Not Modified');
        } else {
            header("Last-Modified: $tsstring");
            header("ETag: \"{$etag}\"");
        }



        header("Expires: $cache_expire_date");
        header("Pragma: cache");
        header("Cache-Control: must-revalidate");
        //header("Cache-Control:max-age=".$cache_length);
        //header("User-Cache-Control:max-age=".$cache_length);
        header("Access-Control-Allow-Methods:GET,HEAD");
        header("Access-Control-Allow-Origin:*");
        header("Accept-Ranges:bytes");


        header($content_type);
        $file = '';
        if (cfs::file_exists($file_path)) {
            $file = file_get_contents($file_path);
        }
        echo $file;
    }

    public function pdf($param_size, $param_filename = null) {
        $app = CApp::instance();
        $org = $app->org();
        $org_code = null;
        if ($org) {
            $org_code = $org->org_code;
        }
        $size = $param_size;
        $filename = $param_filename;
        if ($filename == null) {
            $filename = $size;
            $size = null;
        }
        $filename = CResources::decode($filename);
        $file_path = CResources::get_path($filename, $size);

        if (!cfs::file_exists($file_path)) {
            $file_path = DOCROOT . 'application/admin62hallfamily/default/media/img/product/no-image.png';
        }


        $info = CResources::get_file_info($filename);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $cache_length = 2592000;
        $cache_expire_date = gmdate("D, d M Y H:i:s", time() + $cache_length) . ' GMT';
        $content_type = 'Content-Type:' . carr::get($info, 'resource_type') . '/' . $extension;
        $language = 'ID';

        $timestamp = @filemtime($file_path);

        $tsstring = gmdate('D, d M Y H:i:s ', $timestamp) . 'GMT';
        $etag = $language . $timestamp;
        $etag = md5($filename);

        $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
        $not_modified = false;
        if ($if_modified_since) {
            $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : false;
            $if_modified_since_time = strtotime($if_modified_since);
            $current_time = strtotime(date('Y-m-d H:i:s'));

            if (($current_time - $if_modified_since_time) < $cache_length) {

                $not_modified = true;
            }
        }


        if ($not_modified) {

            header('HTTP/1.1 304 Not Modified');
        } else {
            header("Last-Modified: $tsstring");
            header("ETag: \"{$etag}\"");
        }

        header("Expires: $cache_expire_date");
        header("Pragma: cache");
        header("Cache-Control: max-age:120");
        header("Access-Control-Allow-Methods:GET,HEAD");
        header("Access-Control-Allow-Origin:*");
        header("Accept-Ranges:bytes");

        if (cfs::file_exists($file_path)) {
            header("Content-type:application/pdf");
            header("Content-Disposition:attachment;filename='" . $filename . "'");
            readfile($file_path);
        } else {
            echo 'file [' . $filename . '] not found!';
        }
    }

}
