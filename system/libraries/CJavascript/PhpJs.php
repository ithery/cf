<?php

class CJavascript_PhpJs {
    const JS_SCRIPT_BEGIN = '<script>' . PHP_EOL;

    const JS_SCRIPT_END = '</script>';

    public static function phpToJs($phpCode) {
        $parser = (new \PhpParser\ParserFactory())->create(\PhpParser\ParserFactory::PREFER_PHP7);
        $jsPrinter = new CJavascript_PhpJs_JsPrinter();
        $stmts = $parser->parse($phpCode);

        return $jsPrinter->jsPrint($stmts);
    }
}
