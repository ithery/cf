<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_BladeDirective {
    public static function component($expression) {
        $lastArg = c::str(carr::last(explode(',', $expression)))->trim();

        if ($lastArg->startsWith('key(') && $lastArg->endsWith(')')) {
            /** @var CBase_String $lastArg */
            $cachedKey = $lastArg->replaceFirst('key(', '')->replaceLast(')', '');
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
} elseif (\$_instance->childHasBeenRendered({$cachedKey})) {
    \$componentId = \$_instance->getRenderedChildComponentId({$cachedKey});
    \$componentTag = \$_instance->getRenderedChildComponentTagName({$cachedKey});
    \$html = CApp::component()->dummyMount(\$componentId, \$componentTag);
    \$_instance->preserveRenderedChild({$cachedKey});
} else {
    \$response = CApp::component()->mount({$expression});
    \$html = \$response->html();
    \$_instance->logRenderedChild({$cachedKey}, \$response->id(), CApp::component()->getRootElementTagName(\$html));
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
<?php if ((object) ({$expression}) instanceof \CComponent_CresDirective) : ?>window.cresenity.ui.find('{{ \$_instance->id }}').entangle('{{ {$expression}->value() }}'){{ {$expression}->hasModifier('defer') ? '.defer' : '' }} <?php else : ?> window.cresenity.ui.find('{{ \$_instance->id }}').entangle('{{ {$expression} }}') <?php endif; ?>
EOT;
    }
}
