<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 */
class CApp_Blade_Directive {
    public static function styles($expression) {
        return '{!! CApp::instance()->renderStyles() !!}';
    }

    public static function scripts($expression) {
        return '
        {!! CApp::instance()->renderScripts() !!}
        ';
    }

    public static function pageTitle($expression) {
        return '{!! CApp::instance()->renderPageTitle() !!}';
    }

    public static function title($expression) {
        return '{!! CApp::instance()->renderTitle() !!}';
    }

    public static function nav($expression) {
        return '{!! CApp::instance()->renderNavigation(' . $expression . ') !!}';
    }

    public static function content($expression) {
        return '{!! CApp::instance()->renderContent() !!}';
    }

    public static function pushScript($expression) {
        return '<?php \CApp::instance()->startPush(\'capp-script\') ?>';
    }

    public static function endPushScript($expression) {
        return '<?php \CApp::instance()->stopPush(\'capp-script\'); ?>';
    }

    public static function prependScript($expression) {
        return '<?php \CApp::instance()->startPrepend(\'capp-script\'); ?>';
    }

    public static function endPrependScript($expression) {
        return '<?php \CApp::instance()->stopPrepend(\'capp-script\'); ?>';
    }

    public static function element($expression) {
        $expression = str_replace(['(', ')'], '', $expression);
        $expression = str_replace(['"', '\''], '', $expression);
        $expression = str_replace(',', ' ', $expression);

        $renderingElement = CApp::instance()->renderingElement();

        if ($renderingElement != null) {
            if ($renderingElement instanceof CElement_View) {
                $ownerId = $renderingElement->id();
                return "<?php echo \CApp::instance()->yieldViewElement('" . $expression . "'); ?>";
            } else {
                throw new Exception('Directive CApp Element must be rendered when called from CElement_View');
            }
        }
        return '';
    }

    public static function directive($expression) {
        $expression = str_replace(['(', ')'], '', $expression);
        $expression = str_replace(['"', '\''], '', $expression);
        $expression = str_replace(',', ' ', $expression);
        switch ($expression) {
            case 'styles':
                return static::styles($expression);
            case 'scripts':
                return static::scripts($expression);
            case 'content':
                return static::content($expression);
            case 'pageTitle':
                return static::pageTitle($expression);
            case 'title':
                return static::title($expression);
            default:
                throw new InvalidArgumentException('Argument ' . $expression . ' is invalid on CApp directive');
        }
        return $expression;
    }
}