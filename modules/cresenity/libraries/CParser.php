<?php

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
