<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CElement_Component_Blockly_Helper {

    public static function buildDefaultXmlForFunction($functionName, $arguments = [], $options = []) {
        $x = carr::get($options, 'x', 70);
        $y = carr::get($options, 'y', 70);

        $openXml = '<xml xmlns="https://developers.google.com/blockly/xml">';
        $clsoeXml = '</xml>';
        $openVariables = '<variables>';
        $closeVariables = '</variables>';
        $openBlock = '<block type="procedures_defreturn" x="' . $x . '" y="' . $y . '">';
        $closeBlock = '</block>';
        $openMutation = '<mutation>';
        $openMutation = '</mutation>';

        $variablesXml = '';
        $argsXml = '';
        $fieldXml = '<field name="NAME">' . $functionName . '</field>';
        foreach ($args as $arg) {
            $argsXml .= '<arg name="' . $arg . '"/>';
            $variablesXml = '<variable>' . $arg . '</variable>';
        }
        $argsXml = '<mutation>' . $argsXml . '</mutation>';
        return $openXml . $openBlock . $argsXml . $fieldXml . $closeBlock . $closeXml;
    }

}
