<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CParser {

    public static function createHtmlParser($options = []) {
        return new CParser_HtmlParser($options);
    }

    public static function createJQuery($html, $options = []) {
        return new CParser_JQuery($html, $options);
    }

    public static function cssToInlineStyles() {
        return new CParser_CssToInlineStyles();
    }

}
