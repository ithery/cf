<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 4:04:31 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Controller_Documentation_Validation {

    public function quickstart() {

        $app = CApp::instance();
        $data = array(
            'title'=>'a',
            'body'=>'c',
            'publish_at'=>'a',
            
        );
        $app->validate($data, [
            'title' => 'required|unique:posts|max:255',
            'body' => 'required',
            'publish_at' => 'nullable|date',
        ]);

        echo $app->render();
    }

}
