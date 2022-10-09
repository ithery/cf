<?php

class Controller_Demo_App_Javascript extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Javascript');

        $app->addH5()->addClass('py-4')->add('Don\'t use on PRODUCTION');

        $div = $app->addDiv();

        $js = $div->javascript();
        $js->jquery()->html('Written And modified by Javascript');
        $js->jquery()->addClass('text-success');
        $app->addDiv()->addClass('border-1 py-3');
        $button = $app->addAction();
        $button->onClick(function (CObservable_Javascript $js) {
            $js->jquery()->remove();
        })->setLabel('Click Me To Remove Me')->addClass('btn-primary');

        $app->addDiv()->addClass('border-1 py-3');

        $button = $app->addAction()->addClass('mb-3');

        $divText = $app->addDiv();
        $divText->add('Hello World');
        $divText->customCss('display', 'none');
        $button->onClick(function (CObservable_Javascript $js) use ($divText) {
            $js->jquery($divText)->toggle();
        })->setLabel('Click Me To Show Something')->addClass('btn-primary');

        $app->addHr();
        $divHtml = $app->addDiv();
        $divHtml->add(<<<HTML
            <h2>This is a heading</h2>

            <p>This is a paragraph.</p>
            <p id="test">This is another paragraph.</p>

        HTML);

        $actions = $app->addActionList()->addClass('mb-3');

        $btn = $actions->addAction()->setLabel(c::e('Hide all <p> elements'))->addClass('btn-success');
        $btn->onClick(function (CObservable_Javascript $js) use ($divHtml) {
            $js->jquery($divHtml)->find('p')->hide();
        });
        $btn = $actions->addAction()->setLabel(c::e('Hide all elements with id="test"'))->addClass('btn-success');
        $btn->onClick(function (CObservable_Javascript $js) use ($divHtml) {
            $js->jquery('#test')->hide();
        });

        $divHtml = $app->addDiv()->addClass('d-inline-block my-3 p-3 border-1');
        $divHtml->add('Hover me to see');
        $divHtml->onMouseEnter(function (CObservable_Javascript $js) {
            $js->jquery()->addClass('bg-primary');
        })->onMouseLeave(function (CObservable_Javascript $js) {
            $js->jquery()->removeClass('bg-primary');
        });

        $divWidget = $app->addWidget()->setTitle('Widget Collapsible');
        $actionCollapse = $divWidget->addHeaderAction()->setLabel('Collapse');
        $divWidget->add('Some Content <br/> Another Content');
        $actionCollapse->onClick(function (CObservable_Javascript $js) {
            $js->jquery()->closest('.widget-box')->find('.widget-content')->slideToggle();
            $js->jquery()->toggleClass('btn-primary');
        });

        return $app;
    }

    public function advance() {
        $app = c::app();

        $div = $app->addDiv();
        $button = $app->addAction();
        $button->onClick(function (CObservable_Javascript $js) {
            $js->jquery()->ajax([
                'url' => $this->controllerUrl() . 'ajax',
                'success' => function ($data) {
                    if ($data->errCode == 0) {

                    }
                }
            ]);
        })->setLabel('Click To Ajax')->addClass('btn-primary');

        return $app;
    }

    public function ajax() {
        $errCode = 0;
        $errMessage = '';
        $data = [
            'a' => '1',
            'b' => '2',

        ];

        return CApp_Base::toJsonResponse($errCode, $errMessage, $data);
    }
}
