<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 5:27:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * We have to extend the base HtmlDumper class in order to get access to the protected-only
 * getDumpHeader function.
 */
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class CDebug_DataFormatter_DebugBarVarDumper_DebugBarHtmlDumper extends HtmlDumper {

    public function getDumpHeaderByDebugBar() {
        // getDumpHeader is protected:
        return str_replace('pre.sf-dump', '.phpdebugbar pre.sf-dump', $this->getDumpHeader());
    }

}
