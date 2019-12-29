<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use CElement_Component_Blockly_CategoryHelper as BlockHelper;

class CElement_Component_Blockly_CategoryHelper {

    const CATEGORY_MATH = 'math';
    const CATEGORY_LOOPS = 'loops';
    const CATEGORY_LISTS = 'lists';
    const CATEGORY_LOGIC = 'logic';
    const CATEGORY_VARIABLES = 'variables';
    const CATEGORY_TEXTS = 'texts';
    const CATEGORY_PROCEDURES = 'procedures';
    const CATEGORY_COLOUR = 'colour';
    const CATEGORY_VARIABLES_DYNAMIC = 'variablesDynamic';

    public static $categoryHue = [
        'math' => '230',
        'loops' => '120',
        'lists' => '260',
        'logic' => '210',
        'variables' => '330',
        'texts' => '160',
        'procedures' => '290',
        'colour' => '20',
        'variablesDynamic' => '310',
    ];

    public static function renderCategory($category, $blocksArray) {
        $blockXml = carr::reduce($blocksArray, function($block) {
                    return BlockHelper::renderBlock($block);
                });
        $categoryName = ucwords(str_replace("_", "_", cstr::snakeCase($category)));
        $categoryHue = carr::get(static::$categoryHue, strtolower($category), '230');
        $categoryOpen = '<category name="' . $categoryName . '" colour="' . $categoryHue . '">';
        $categoryClose = '</category>';
        return $categoryOpen . $blockXml . $categoryClose;
    }

}
