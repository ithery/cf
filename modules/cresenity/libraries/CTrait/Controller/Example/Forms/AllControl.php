<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 4:09:45 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Controller_Example_Forms_AllControl {

    public function example() {
        $app = CApp::instance();

        $app->addForm();
        $app->addField()->setLabel('Text')->addControl('textControl', 'text');
        $app->addField()->setLabel('Textarea')->addControl('textareaControl', 'textarea');
        $app->addField()->setLabel('Date')->addControl('dateControl', 'date');


        echo $app->render();
    }

}
