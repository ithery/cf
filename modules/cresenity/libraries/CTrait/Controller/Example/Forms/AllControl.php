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
        $app->addField()->setLabel('Password')->addControl('passwordControl', 'password');
        $app->addField()->setLabel('Textarea')->addControl('textareaControl', 'textarea');
        $app->addField()->setLabel('Number')->addControl('numberControl', 'number');
        $app->addField()->setLabel('Email')->addControl('emailControl', 'email');
        $app->addField()->setLabel('Currency')->addControl('currencyControl', 'currency');
        $app->addField()->setLabel('Date')->addControl('dateControl', 'date');
        $app->addField()->setLabel('Time')->addControl('timeControl', 'time');
        $app->addField()->setLabel('Clock')->addControl('clockControl', 'clockpicker');
        $app->addField()->setLabel('Image')->addControl('imageControl', 'image');
        $app->addField()->setLabel('Image Ajax')->addControl('imageAjaxControl', 'image-ajax');


        echo $app->render();
    }

}
