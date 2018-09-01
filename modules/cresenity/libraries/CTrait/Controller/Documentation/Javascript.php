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

        echo $app->render();
    }

}
