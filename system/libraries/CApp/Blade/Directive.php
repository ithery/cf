<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 *
 * @see CApp
 */
class CApp_Blade_Directive {
    public static function styles($expression) {
        return '{!! CApp::instance()->renderStyles() !!}';
    }

    public static function message($expression) {
        return '{!! CApp_Message::flashAll() !!}';
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

    public static function seo($expression) {
        return '{!! CApp::instance()->renderSeo() !!}';
    }

    public static function content($expression) {
        return '{!! CApp::instance()->renderContent() !!}';
    }

    public static function react($expression) {
        return '{!! CApp_React::render(' . $expression . ') !!}';
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
        if (cstr::startsWith(trim($expression), 'function')) {
            return "<?php echo \CApp::instance()->yieldViewElement(isset(\$__CAppElementView) ? \$__CAppElementView : null, " . $expression . '); ?>';
        }
        $expression = str_replace(['(', ')'], '', $expression);
        $expression = str_replace(['"', '\''], '', $expression);
        $expression = str_replace(',', ' ', $expression);

        return "<?php echo \CApp::instance()->yieldViewElement(isset(\$__CAppElementView) ? \$__CAppElementView : null, '" . $expression . "'); ?>";
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

    public static function pwa($expression) {
        $expression = str_replace(['(', ')'], '', $expression);
        $expression = str_replace(['"', '\''], '', $expression);
        $expression = str_replace(',', ' ', $expression);

        return (new CApp_PWA_MetaService($expression))->render();
    }

    public static function preloader($expression) {
        if (strlen($expression) == 0) {
            $expression = c::url('media/img/logo.png');
        }

        return <<<HTML
<!-- Cres Preloader Start Here ${expression} -->
<div id="cres-preloader">
    <div class="preloader-container">
        <div class="preloader-loader">
        </div>
        <img src="<?php echo ${expression}; ?>" />
    </div>
</div>

<!-- Cres Preloader End Here -->
HTML;
    }
}
