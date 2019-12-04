<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 10:58:14 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_FormProcess extends CAjax_Engine {

    public function execute() {
        $db = CDatabase::instance();
        $input = $this->input;
        $data = $this->ajaxMethod->getData();
        $form = carr::get($data, 'form');
        $process_id = "";

        if (isset($input["ajax_process_id"])) {
            $process_id = $input["ajax_process_id"];
        }
        if (isset($input["ajax_process_id"])) {
            $last_process_id = cprogress::last_process_id();
        }
        if (isset($input["cancel"])) {
            $filename = $process_id . "_cancel". ".tmp";
            $disk = CTemporary::disk();
            
            $file = CTemporary::getPath("process", $filename);
            $json = $disk->put($file, $form);
            echo json_encode(array(
                "result" => "0",
                "message" => "User cancelled",
            ));
            die();
        }

        $filename = $process_id . ".tmp";
        $disk = CTemporary::disk();
        $file = CTemporary::getPath("process", $filename);

        $json = '{"percent":0,"info":"Initializing"}';
        if ($disk->exists($file)) {
            $json = $disk->get($file);
        }
        echo $json;
    }

}
