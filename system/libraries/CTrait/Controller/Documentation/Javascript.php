<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 4:09:40 AM
 */
trait CTrait_Controller_Documentation_Javascript {
    public function statement() {
        $app = CApp::instance();
        $app->title(c::__('Javascript Statement'));

        $app->addH4('Raw Statement');

        $code = '
$p = $app->addP();
$p->add("This is P Element");
CManager::javascript()->jquery()->addClass($p,"alert alert-warning");
        ';
        $app->addDiv()->addPrismCode()->add(trim($code));

        $p = $app->addP();
        $p->add('This is P Element');
        CManager::javascript()->jquery()->addClass($p, 'alert alert-warning');

        $code = '
$p = $app->addP();
$p->add("This is P Element");
CManager::javascript()->jquery()->addClass($p,"alert alert-warning");
CManager::javascript()->jquery()->append($p, "<br/><div class=\"mt-3\">Append from Javascript</div>");
        ';
        $app->addDiv()->addPrismCode()->add(trim($code));

        $p = $app->addP();
        $p->add('This is P Element');
        CManager::javascript()->jquery()->addClass($p, 'alert alert-warning');
        CManager::javascript()->jquery()->append($p, '<br/><div class="mt-3">Append from Javascript</div>');

        $input1 = $app->addControl('input-1', 'text');
        $app->addDiv('message-1');
        CManager::javascript()->jquery()->onBlur($input1, "$('#message-1').html('blur');");

        echo $app->render();
    }

    public function event($ajax = false) {
        if ($ajax) {
            echo 'ajax done';
            die;
        }
        $app = CApp::instance();

        $divAppendPrepend = $app->addDiv()->add('Div After Before');

        $divAppendPrepend->jquery()->after('<div>After</div>');
        $divAppendPrepend->jquery()->before('<div>Before</div>');

        $divAppendPrepend = $app->addDiv()->add('Content');
        $divAppendPrepend->jquery()->prepend('Prepend');
        $divAppendPrepend->jquery()->append('Append');

        $divHtml = $app->addDiv()->add('HTML');
        $divHtml->jquery()->html('Changed HTML');
        $divHtml->jquery()->addClass('mt-3');

        $button = $app->addAction()->setLabel('Click Me');

        $button->jquery()->addClass('btn-primary');
        $button->jquery()->addClass('btn-primary');
        $button->onClick(function ($js) use ($divHtml) {
            $js->bindDeferred($divHtml, function ($divHtmlJq) use ($js) {
                $divHtmlJq->jquery()->html('');
            });
            $js->jquery()->appendTo($divHtml);
        });
        echo $app->render();
    }

    public function form() {
        $app = CApp::instance();

        $list = [
            'option1' => 'Option 1',
            'option2' => 'Option 2',
            'option3' => 'Option 3',
            'option4' => 'Option 4',
        ];

        $list2 = [
            'list1' => 'List 1',
            'list2' => 'List 2',
            'list3' => 'List 3',
            'list4' => 'List 4',
        ];

        $form = $app->addForm();
        $select = $form->addField()->setLabel('Select')->addControl('optionSelect', 'select')->setList($list);

        $div = $app->addDiv();
        $divAjax = $app->addDiv('divAjax');
        $select->onChange(function ($selectJs) use ($div, $divAjax) {
            $ajaxOptions = [];
            $ajaxOptions['url'] = curl::base() . 'documentation/javascript/ajax';
            $ajaxOptions['dataType'] = 'json';
            $ajaxOptions['data'] = [];
            $ajaxOptions['success'] = function ($data) use ($divAjax) {
                $divAjax->jquery()->html($data->html);
            };
            $div->jquery()->ajax($ajaxOptions);
        });

        $select->jquery()->trigger('change');

        echo $app->render();
    }

    public function ajax() {
        $data['html'] = 'html 4';
        $data['js'] = 'js';
        die;
        echo json_encode($data);
    }

    public function reload2() {
        $app = CApp::instance();

        $list = [
            'option1' => 'Option 1',
            'option2' => 'Option 2',
            'option3' => 'Option 3',
            'option4' => 'Option 4',
        ];

        $form = $app->addForm();
        $select = $form->addField()->setLabel('Select')->addControl('optionSelect', 'select')->setList($list);
        $action = $form->addActionList()->addAction();
        $action->setLabel('Submit')->addClass('btn-primary');

        $divAjax = $app->addDiv('divAjax');
        $action->onClick(function ($selectJs) use ($divAjax) {
            $ajaxOptions = [];
            $ajaxOptions['url'] = curl::base() . 'documentation/javascript/ajax';
            $ajaxOptions['dataType'] = 'json';
            $ajaxOptions['data'] = [];
            $ajaxOptions['success'] = function ($data) use ($divAjax) {
                $divAjax->jquery()->html($data->html);
            };
            $ajaxOptions['error'] = function ($jqXhr, $textStatus, $errorThrown) use ($divAjax) {
                $divAjax->jquery()->html($jqXhr->statusText);
            };
            $divAjax->jquery()->ajax($ajaxOptions);
        });

        $select->jquery()->trigger('change');
        echo $app->render();
    }

    public function reload() {
        $app = CApp::instance();

        $action = $app->addAction()->setLabel('Hide Me')->addClass('btn-primary');
        $action2 = $app->addAction()->setLabel('Show Me')->addClass('btn-primary');
        $div = $app->addDiv();
        $div->add('Test');
        $action->onClick(function ($actionJs) use ($div) {
            $div->jquery()->hide();
        });
        $action2->onClick(function ($actionJs) use ($div) {
            $div->jquery()->show();
        });

        echo $app->render();
    }

    public function test() {
        $app = CApp::instance();

        $data = new CJavascript_Mock_Variable('data');
        $test = $data->test;
        $test2 = $data->test->test2;

        $test3 = $test->test3;
        $test4 = $data->variable;
        $app->add($test->getScript());
        $app->addHr();
        $app->add($test2->getScript());
        $app->addHr();
        $app->add($test3->getScript());
        $app->addHr();
        $app->add($test4->getScript());
        echo $app->render();
    }
}
