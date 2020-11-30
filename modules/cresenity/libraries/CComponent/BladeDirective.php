<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_BladeDirective {

    public static function component($expression) {

        $lastArg = trim(carr::last(explode(',', $expression)));

        if (cstr::startsWith($lastArg, 'key(') && cstr::endsWith($lastArg, ')')) {
            $cachedKey = cstr::replaceFirst($lastArg, 'key(', '')->replaceLast(')', '');
            $args = explode(',', $expression);
            array_pop($args);
            $expression = implode(',', $args);
        } else {
            $cachedKey = "'" . cstr::random(7) . "'";
        }


        return <<<EOT
<?php
if (! isset(\$_instance)) {
    \$html = CApp::component()->mount({$expression})->html();
} elseif (\$_instance->childHasBeenRendered($cachedKey)) {
    \$componentId = \$_instance->getRenderedChildComponentId($cachedKey);
    \$componentTag = \$_instance->getRenderedChildComponentTagName($cachedKey);
    \$html = CApp::component()->dummyMount(\$componentId, \$componentTag);
    \$_instance->preserveRenderedChild($cachedKey);
} else {
    \$response = CApp::component()->mount({$expression});
    \$html = \$response->html();
    \$_instance->logRenderedChild($cachedKey, \$response->id(), CApp::component()->getRootElementTagName(\$html));
}
echo \$html;
?>
EOT;
    }

    public static function this() {
        return "window.cresenity.ui.find('{{ \$_instance->id }}')";
    }

    public static function entangle($expression) {
        return <<<EOT
<?php if ((object) ({$expression}) instanceof \CComponent_CFDirective) : ?>window.cresenity.ui.find('{{ \$_instance->id }}').entangle('{{ {$expression}->value() }}'){{ {$expression}->hasModifier('defer') ? '.defer' : '' }} <?php else : ?> window.cresenity.ui.find('{{ \$_instance->id }}').entangle('{{ {$expression} }}') <?php endif; ?>
EOT;
    }

}
