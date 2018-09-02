<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 4:09:40 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Controller_Documentation_Javascript {

    public function statement() {
        $app = CApp::instance();
        $app->title(clang::__("Javascript Statement"));

        $app->addH4('Raw Statement');



        $code = '
            $p = $app->addP();
            $p->add("This is P Element");
            CManager::javascript()->jquery()->addClass($p,"alert alert-warning");
        ';
        $app->addDiv()->addClass('my-2 console')->add(trim($code));

        $p = $app->addP();
        $p->add("This is P Element");
        CManager::javascript()->jquery()->addClass($p, "alert alert-warning");

        $code = '
            $p = $app->addP();
            $p->add("This is P Element");
            CManager::javascript()->jquery()->addClass($p,"alert alert-warning");
            CManager::javascript()->jquery()->append($p, "<br/><div class=\"mt-3\">Append from Javascript</div>");
        ';
        $app->addDiv()->addClass('my-2 console')->add(trim(htmlspecialchars($code)));

        $p = $app->addP();
        $p->add("This is P Element");
        CManager::javascript()->jquery()->addClass($p, "alert alert-warning");
        CManager::javascript()->jquery()->append($p, "<br/><div class=\"mt-3\">Append from Javascript</div>");

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

        $divAppendPrepend = $app->addDiv()->add("Div After Before");


        $divAppendPrepend->jquery()->after('<div>After</div>');
        $divAppendPrepend->jquery()->before('<div>Before</div>');

        $divAppendPrepend = $app->addDiv()->add("Content");
        $divAppendPrepend->jquery()->prepend('Prepend');
        $divAppendPrepend->jquery()->append('Append');


        $divHtml = $app->addDiv()->add("HTML");
        $divHtml->jquery()->html('Changed HTML');
        $divHtml->jquery()->addClass('mt-3');

        $button = $app->addAction()->setLabel('Click Me');

        $button->jquery()->addClass('btn-primary');
        $button->jquery()->addClass('btn-primary');
        $button->onClick(function($js) use ($divHtml) {
            $js->bindDeferred($divHtml, function($divHtmlJq) use($js) {
                $divHtmlJq->jquery()->html('');
            });
            $js->jquery()->appendTo($divHtml);
        });
        echo $app->render();
    }

    public function form() {
        $app = CApp::instance();

        $list = array(
            'option1' => 'Option 1',
            'option2' => 'Option 2',
            'option3' => 'Option 3',
            'option4' => 'Option 4',
        );

        $form = $app->addForm();
        $select = $form->addField()->setLabel('Select')->addControl('optionSelect', 'select')->setList($list);

        $div = $app->addDiv();
        $divAjax = $app->addDiv('divAjax');
        $select->onChange(function($selectJs) use($div) {
            $selectJs->bindDeferred($div, function($divJs) use($selectJs) {
//                $divJs->jquery()->html($selectJs->jquery()->find(':selected')->html());
//                $divJs->variable('a', 'test');
                $ajaxOptions = array();
                $ajaxOptions['url'] = curl::base() . 'documentation/javascript/ajax';
                $ajaxOptions['success'] = function($divJsAjax,$data) {
                    $divJsAjax->raw('$("#divAjax").html(data);');
                };
                $divJs->jquery()->ajax($ajaxOptions);
            });
        });

        echo $app->render();
    }

    public function ajax() {
        echo 'Ajax Content';
    }

    public function test() {
        $app = CApp::instance();

        $div = $app->addDiv('test')->add('Haha');
        $div->jquery()->addClass('btn btn-primary');

        echo $app->render();
    }

}
