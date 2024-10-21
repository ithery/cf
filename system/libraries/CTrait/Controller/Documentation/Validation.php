<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2018, 4:04:31 AM
 */
trait CTrait_Controller_Documentation_Validation {
    public function quickstart() {
        $app = CApp::instance();
        $data = [
            'title' => 'a',
            'body' => 'c',
            'publish_at' => 'a',
        ];
        $app->validate($data, [
            'title' => 'required|unique:posts|max:255',
            'body' => 'required',
            'publish_at' => 'nullable|date',
        ]);

        return $app;
    }
}
