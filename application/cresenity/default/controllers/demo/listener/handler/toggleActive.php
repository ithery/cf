<?php

class Controller_Demo_Listener_Handler_ToggleActive extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Toggle Active');
        $app->add(<<<HTML
        <style>
        .cursor-pointer {
            cursor: pointer;
        }
        .row-menu .col-md-4 .menu-item {
            border:1px solid #333;
            padding:10px 20px;
        }
        .row-menu .col-md-4.active .menu-item {
            border:1px solid #CC131F;
            background:rgba(204,19,21,.2);
        }
        .result {
            border:1px solid #333;
            padding:10px 20px;
        }
        </style>
        HTML);
        $divRow = $app->addDiv()->addClass('row row-menu');
        $divRow->addDiv('menu-1')->addClass('col-md-4')->addDiv()->addClass('menu-item')->add('Menu 1');
        $divRow->addDiv('menu-2')->addClass('col-md-4')->addDiv()->addClass('menu-item')->add('Menu 2');
        $divRow->addDiv('menu-3')->addClass('col-md-4')->addDiv()->addClass('menu-item')->add('Menu 3');
        $divResult = $app->addDiv()->addClass('result mt-5');
        $cols = c::collect($divRow->childs())->each(function (CElement_Element_Div $div) use ($divResult) {
            $div->addClass('cursor-pointer');
            $listener = $div->onClickListener();
            $listener->addToggleActiveHandler();
            $listener->addReloadHandler()->setTarget($divResult)->setUrl($this->controllerUrl() . 'reload?menu=' . $div->id());
            $listener->addToastHandler()->setType('info')->setMessage('Toast from ' . $div->id());
        });
        $firstCol = $cols->first();
        $firstCol->addClass('active');

        $this->reload($divResult, ['menu' => $firstCol->id()]);

        return $app;
    }

    public function reload($container = null, $options = []) {
        $app = $container ?: c::app();
        /** @var CApp $app */
        $request = array_merge($options, c::request()->all());

        $app->add('Reloaded from: ' . carr::get($request, 'menu'));

        return $app;
    }
}
