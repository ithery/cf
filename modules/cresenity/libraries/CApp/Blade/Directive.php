<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */

class CApp_Blade_Directive {
    
    
    public function styles($expression) {
        return '{!! CApp::instance()->renderStyles('.$expression.') !!}';
    }
    
    public function scripts($expression) {
        return '{!! CApp::instance()->renderScripts('.$expression.') !!}';
    }
    public function pageTitle($expression) {
        return '{!! CApp::instance()->renderPageTitle('.$expression.') !!}';
    }
    
    public function title($expression) {
        return '{!! CApp::instance()->renderTitle('.$expression.') !!}';
    }
    
    public function content($expression) {
        return '{!! CApp::instance()->renderContent('.$expression.') !!}';
    }
    
    public function directive($expression) {
        die($expression);
    }
}