<?php

defined('SYSPATH') or die('No direct access allowed.');

class Controller_Ccore extends CController {
    public function index() {
        curl::redirect('');
    }

    public function js($hash = null) {
        header('content-type: application/javascript');
        return '';
    }

    public function css($hash = null) {
        header('content-type: text/css');
        return '';
    }

    public function noimage($width = 200, $height = 150, $bg_color = 'EFEFEF', $txt_color = 'AAAAAA', $text = 'NO IMAGE') {
        //Create the image resource
        $image = imagecreate($width, $height);
        //Making of colors, we are changing HEX to RGB
        $bg_color = imagecolorallocate($image, base_convert(substr($bg_color, 0, 2), 16, 10), base_convert(substr($bg_color, 2, 2), 16, 10), base_convert(substr($bg_color, 4, 2), 16, 10));

        $txt_color = imagecolorallocate($image, base_convert(substr($txt_color, 0, 2), 16, 10), base_convert(substr($txt_color, 2, 2), 16, 10), base_convert(substr($txt_color, 4, 2), 16, 10));

        //Fill the background color
        imagefill($image, 0, 0, $bg_color);
        //Calculating font size
        $fontsize = ($width > $height) ? ($height / 10) : ($width / 10);
        if ($width < 100) {
            $fontsize = 1;
        }
        $line_number = 1;
        $total_lines = 1;
        $center_x = ceil((imagesx($image) - (imagefontwidth($fontsize) * strlen($text))) / 2);
        $center_y = ceil(((imagesy($image) - (imagefontheight($fontsize) * $total_lines)) / 2) + (($line_number - 1) * imagefontheight($fontsize)));
        //Inserting Text
        imagestring($image, $fontsize, $center_x, $center_y, $text, $txt_color);
        /*
          imagettftext($image,$fontsize, 0,
          ($width/2) - ($fontsize * 2.75),
          ($height/2) + ($fontsize* 0.2),
          $txt_color, 'Arial.ttf', $text);
         */

        //Tell the browser what kind of file is come in
        header('Content-Type: image/png');
        //Output the newly created image in png format
        imagepng($image);
        //Free up resources
        ImageDestroy($image);
    }

    public function ajax($method) {
        $filename = $method . '.tmp';
        $file = CTemporary::getLocalPath('ajax', $filename);

        if (!file_exists($file)) {
            cdbg::dd($file);
            return;
        }
        $text = file_get_contents($file);
        $obj = json_decode($text);
        $response = '';
        $db = CDatabase::instance();
        $input = $_POST;
        if ($obj->method == 'GET') {
            $input = $_GET;
        }

        switch ($obj->type) {
            case 'form_process':
                $response = cajax::form_process($obj, $input);
                break;
            case 'query':
                $response = cajax::query($obj, $input);
                break;
            case 'fillselect':
                $response = cajax::fillselect($obj, $input);
                break;
            case 'searchselect':
                $response = cajax::searchselect($obj, $input);
                break;
            case 'datatable':
                $response = cajax::datatable($obj, $input);
                break;
            case 'callback':
                $response = cajax::callback($obj, $input);
                break;
            case 'fileupload':
                $response = cajax::fileupload($obj, $input);
                break;
            case 'imgupload':
                $response = cajax::imgupload($obj, $input);
                break;
            case 'dialogselect':
                $response = cajax::dialogselect($obj, $input);
                break;
            case 'modaldialogselect':
                $response = cajax::modaldialogselect($obj, $input);
                break;
            case 'reload':
            case 'handler_reload':
                $response = cajax::handler_reload($obj, $input);
                break;
        }
        echo $response;
    }
}
