<?php
defined('SYSPATH') or die('No direct access allowed.');

/**
 * @deprecated 1.2
 */
class CChartPie extends CElement {
    public function __construct($id = '') {
        parent::__construct($id);
    }

    public static function factory($id = '') {
        return new CChartPie($id);
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $disabled = '';
        $html->appendln('<span>');

        $html->appendln(parent::html($html->getIndent()))->br();
        $html->appendln('</span>');

        return $html->text();
    }

    public function js($indent = 0) {
        return parent::js($indent);
    }
}
