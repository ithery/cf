<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Parser {
    public static function parse($xml, $options = [], $includedIn = []) {
        $defaultOptions = array(
            'addEmptyAttributes'=>true,
            'components'=>[],
            'convertBooleans'=>true,
            'addEmptyAttributes'=>true,
            'filePath'=>'.',
            'ignoreIncludes'=>false,
        );
        $options = array_merge($defaultOptions,$options);
     
        $endingTags = c::flow(
    filter(component => component.endingTag),
    map(component => component.getTagName()),
  )({ ...components })
    }
}