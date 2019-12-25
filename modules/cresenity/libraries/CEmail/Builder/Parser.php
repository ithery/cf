<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Parser {

    public static function toHtml($cml, $options = []) {
        $content = '';
        $errors = [];
        $defaultFonts = [];
        $defaultFonts['Open Sans'] = 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,700';
        $defaultFonts['Droid Sans'] = 'https://fonts.googleapis.com/css?family=Droid+Sans:300,400,500,700';
        $defaultFonts['Lato'] = 'https://fonts.googleapis.com/css?family=Lato:300,400,500,700';
        $defaultFonts['Roboto'] = 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700';
        $defaultFonts['Ubuntu'] = 'https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700';

        $cnode = $cml;
        $beautify = carr::get($options, 'beautify', false);
        $fonts = carr::get($options, 'fonts', $defaultFonts);
        $keepComments = carr::get($options, 'keepComments', false);
        $minify = carr::get($options, 'minify', false);
        $minifyOptions = carr::get($options, 'minifyOptions', []);
        $validationLevel = carr::get($options, 'validationLevel', 'soft');
        $filePath = carr::get($options, 'filePath', '.');



        if (is_string($cnode)) {
            $parserOptions = [];
            $parserOptions['keepComments'] = $keepComments;
            $parserOptions['components'] = CEmail::builder()->components();
            $parserOptions['filePath'] = $filePath;
            $cmlParser = new CEmail_Builder_Parser_CmlParser($cnode, $parserOptions);
            $cnode = $cmlParser->parse();
        }


        $globalData = array();
        $globalData['backgroundColor'] = '';
        $globalData['breakpoiunt'] = '480px';
        $globalData['classes'] = [];
        $globalData['classesDefault'] = [];
        $globalData['defaultAttributes'] = [];
        $globalData['fonts'] = $fonts;
        $globalData['inlineStyle'] = [];
        $globalData['headStyle'] = [];
        $globalData['componentHeadStyle'] = [];
        $globalData['headRaw'] = [];
        $globalData['mediaQueries'] = [];
        $globalData['preview'] = '';
        $globalData['style'] = [];
        $globalData['title'] = '';
        $globalData['forceOWADesktop'] = CF::get($cnode, 'attributes.owa', 'mobile') === 'desktop';
        $globalData['lang'] = CF::get($cnode, 'attributes.lang');






        return $content;
    }

}
